<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Supply;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class BarcodeController extends Controller
{
    public function generator(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $supplies = collect();
        $assets = collect();

        if ($category === 'all' || $category === 'supply') {
            $supplyQuery = Supply::whereNotNull('barcode_id')
                ->select('id', 'barcode_id as barcode_code', 'article', 'description', 'supplier');
                
            if (!empty($search)) {
                $supplyQuery->where(function($q) use ($search) {
                    $q->where('article', 'LIKE', "%{$search}%")
                      ->orWhere('barcode_id', 'LIKE', "%{$search}%");
                });
            }

            $supplies = $supplyQuery->get()->map(function ($item) {
                $item->item_type = 'supply';
                $item->generated_at = null; 
                return $item;
            });
        }

        if ($category === 'all' || $category === 'asset') {
            $assetQuery = Asset::whereNotNull('barcode_id')
                ->select('id', 'barcode_id as barcode_code', 'article', 'description', 'supplier');
                
            if (!empty($search)) {
                $assetQuery->where(function($q) use ($search) {
                    $q->where('article', 'LIKE', "%{$search}%")
                      ->orWhere('barcode_id', 'LIKE', "%{$search}%");
                });
            }

            $assets = $assetQuery->get()->map(function ($item) {
                $item->item_type = 'asset';
                $item->generated_at = null; 
                return $item;
            });
        }

        $mergedBarcodes = $supplies->concat($assets)->sortByDesc('id')->values();

        $offset = ($page * $perPage) - $perPage;
        $itemsForCurrentPage = $mergedBarcodes->slice($offset, $perPage)->all();
        
        $barcodes = new LengthAwarePaginator(
            $itemsForCurrentPage, 
            $mergedBarcodes->count(), 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('barcodes.generator', compact('barcodes', 'perPage', 'search', 'category'));
    }

    public function processScan(Request $request)
    {
        $barcode = trim($request->barcode);
        $qty = intval($request->qty);
        $mode = strtoupper($request->mode);
        $context = strtolower(trim($request->context ?? 'all'));
        $risNumber = trim($request->ris_number);

        $item = null;
        $table = '';

        if ($context === 'supplies') {
            $item = Supply::where('barcode_id', $barcode)->first();
            $table = 'supplies';
        } elseif ($context === 'assets') {
            $item = Asset::where('barcode_id', $barcode)->first();
            $table = 'assets';
        } else {
            $item = Asset::where('barcode_id', $barcode)->first();
            $table = 'assets';
            if (!$item) {
                $item = Supply::where('barcode_id', $barcode)->first();
                $table = 'supplies';
            }
        }

        if ($item) {
            $new_stock = ($mode == 'IN') ? ($item->quantity + $qty) : ($item->quantity - $qty);

            if ($new_stock < 0) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient Stock Available']);
            }

            $item->update(['quantity' => $new_stock]);

            $remarks = ($mode == 'OUT' && !empty($risNumber)) ? 'RIS: ' . $risNumber : 'Scanner';

            Transaction::create([
                'item_id' => $item->id,
                'item_type' => $table,
                'transaction_type' => $mode,
                'quantity' => $qty,
                'transaction_date' => date('Y-m-d'),
                'remarks' => $remarks
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated',
                'description' => "Processed Scanner {$mode} for Barcode: {$barcode} (Qty: {$qty})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return response()->json([
                'status' => 'success',
                'item_name' => $item->article,
                'new_stock' => $new_stock,
                'barcode' => $barcode,
                'mode' => $mode,
                'qty' => $qty
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Barcode not found in ' . ucfirst($context) . ' Inventory.']);
    }

    public function recentScans(Request $request)
    {
        $context = $request->context ?? 'all';
        
        $query = Transaction::where(function($q) {
            $q->where('remarks', 'Scanner')->orWhere('remarks', 'LIKE', 'RIS:%');
        })->orderBy('id', 'desc')->limit(10);

        if ($context == 'assets') {
            $query->where('item_type', 'assets');
        } elseif ($context == 'supplies') {
            $query->where('item_type', 'supplies');
        }

        $transactions = $query->get();
        $html = '';

        if ($transactions->count() > 0) {
            foreach ($transactions as $t) {
                $mode = strtoupper($t->transaction_type);
                $color = ($mode == 'IN') ? 'success' : 'danger';
                
                $itemName = 'Deleted Item';
                $barcode = 'N/A';
                
                if ($t->item_type == 'assets') {
                    $asset = Asset::find($t->item_id);
                    if ($asset) { $itemName = $asset->article; $barcode = $asset->barcode_id; }
                } else {
                    $supply = Supply::find($t->item_id);
                    if ($supply) { $itemName = $supply->article; $barcode = $supply->barcode_id; }
                }

                $html .= '<div class="list-group-item d-flex justify-content-between align-items-center bg-'.$color.' bg-opacity-10 border-start border-'.$color.' border-4 mb-2 shadow-sm rounded">
                            <div><div class="fw-bold text-dark">'.$itemName.'</div><small class="text-muted">'.$barcode.'</small></div>
                            <div class="text-end"><span class="badge bg-'.$color.'">'.$mode.' '.$t->quantity.'</span></div>
                          </div>';
            }
        }
        return response($html);
    }

    public function printAll(Request $request)
    {
        $type = $request->query('type', 'supply');
        
        if ($type === 'asset') {
            $items = Asset::whereNotNull('barcode_id')->orderBy('article', 'asc')->get();
            $title = "Asset Barcodes Master List";
        } else {
            $items = Supply::whereNotNull('barcode_id')->orderBy('article', 'asc')->get();
            $title = "Supply Barcodes Master List";
        }

        $html = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>'.$title.'</title>
            <style>
                body { font-family: "Segoe UI", sans-serif; padding: 20px; }
                .text-center { text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px 8px; text-align: left; vertical-align: middle; }
                th { background-color: #f4f6f9; color: #101954; font-weight: bold; }
                .barcode-cell { text-align: center; width: 250px; }
                .desc { font-size: 0.85rem; color: #555; }
                @media print { 
                    button { display: none; } 
                    @page { margin: 10mm; }
                }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
        </head>
        <body>
            <h2 class="text-center" style="color: #101954; margin-bottom: 5px;">'.$title.'</h2>
            <p class="text-center" style="color: #666; margin-top: 0;">Generated on ' . date('M d, Y') . '</p>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30%">Article / Item</th>
                        <th style="width: 40%">Description</th>
                        <th class="barcode-cell">Barcode</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($items as $item) {
            $desc = htmlspecialchars($item->description ?? 'No description available');
            $code = $item->barcode_id;
            $article = htmlspecialchars($item->article);
            
            $html .= '<tr>
                        <td><strong>' . $article . '</strong></td>
                        <td class="desc">' . $desc . '</td>
                        <td class="barcode-cell">
                            <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="'.$code.'" jsbarcode-displayvalue="true" jsbarcode-height="40" jsbarcode-width="1.5"></svg>
                        </td>
                      </tr>';
        }

        $html .= '</tbody>
            </table>
            <script>
                JsBarcode(".barcode").init();
                setTimeout(function() { 
                    window.print(); 
                }, 800);
            </script>
        </body>
        </html>';

        return response($html);
    }
}