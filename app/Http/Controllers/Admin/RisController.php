<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RisRequest;
use App\Models\Supply;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RisController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 7);

        $requests = RisRequest::where('status', '!=', 'Pending Staff Review')
                              ->orderByRaw("CASE WHEN status = 'Forwarded to Admin' THEN 1 ELSE 2 END")
                              ->orderBy('created_at', 'desc')
                              ->paginate($perPage);
                              
        return view('admin.ris.index', compact('requests', 'perPage'));
    }

    public function review($id)
    {
        $req = RisRequest::with('items')->findOrFail($id);
        return view('admin.ris.verify_modal', compact('req'))->render();
    }

    public function process(Request $request, $id)
    {
        $ris = RisRequest::with('items')->findOrFail($id);
        $new_status = $request->new_status; 
        
        // --- 1. AUTO-DEDUCT STOCKS IF NEWLY APPROVED ---
        if ($new_status == 'Approved' && $ris->status != 'Approved') {
            foreach ($ris->items as $item) {
                
                // FIXED: Fallback to Requested Quantity if Issue Quantity is blank or 0
                $issueQty = !empty($item->issue_quantity) && $item->issue_quantity > 0 
                            ? (float) $item->issue_quantity 
                            : (float) $item->req_quantity;

                if ($issueQty > 0) {
                    
                    $supply = null;
                    
                    // 1. Try finding by Exact Barcode/Stock No first
                    if (!empty(trim($item->stock_no))) {
                        $supply = Supply::where('barcode_id', trim($item->stock_no))->first();
                    }
                    
                    // 2. Fallback: Try fuzzy matching by Description or Article if Barcode is empty/wrong
                    if (!$supply && !empty(trim($item->description))) {
                         $desc = trim($item->description);
                         $supply = Supply::where('article', 'LIKE', "%{$desc}%")
                                         ->orWhere('description', 'LIKE', "%{$desc}%")
                                         ->first();
                    }
                    
                    // If a matching supply is found, deduct the quantity and log it
                    if ($supply) {
                        $supply->decrement('quantity', $issueQty); 
                        
                        Transaction::create([
                            'item_id' => $supply->id,
                            'item_type' => 'supplies',
                            'transaction_type' => 'OUT',
                            'quantity' => $issueQty,
                            'supplier' => $supply->supplier,
                            'transaction_date' => now()->toDateString(),
                            'remarks' => 'RIS Auto-Release: ' . $ris->ris_no
                        ]);
                    }
                }
            }
        }

        // --- 2. AUTO-RESTORE STOCKS IF ADMIN REVOKES APPROVAL ---
        if ($new_status == 'Pending Staff Review' && $ris->status == 'Approved') {
            foreach ($ris->items as $item) {
                
                $issueQty = !empty($item->issue_quantity) && $item->issue_quantity > 0 
                            ? (float) $item->issue_quantity 
                            : (float) $item->req_quantity;

                if ($issueQty > 0) {
                    
                    $supply = null;
                    
                    if (!empty(trim($item->stock_no))) {
                        $supply = Supply::where('barcode_id', trim($item->stock_no))->first();
                    }
                    
                    if (!$supply && !empty(trim($item->description))) {
                         $desc = trim($item->description);
                         $supply = Supply::where('article', 'LIKE', "%{$desc}%")
                                         ->orWhere('description', 'LIKE', "%{$desc}%")
                                         ->first();
                    }
                    
                    if ($supply) {
                        $supply->increment('quantity', $issueQty); 
                        
                        Transaction::create([
                            'item_id' => $supply->id,
                            'item_type' => 'supplies',
                            'transaction_type' => 'IN',
                            'quantity' => $issueQty,
                            'supplier' => $supply->supplier,
                            'transaction_date' => now()->toDateString(),
                            'remarks' => 'RIS Revoked/Returned: ' . $ris->ris_no
                        ]);
                    }
                }
            }
        }

        $ris->update([
            'status' => $new_status,
            'date_approved' => $new_status == 'Approved' ? now()->toDateString() : ($new_status == 'Pending Staff Review' ? null : $ris->date_approved),
        ]);

        $msg = "RIS successfully updated to " . strtolower($new_status) . "!";
        return redirect('/admin/ris')->with('msg', $msg);
    }
}