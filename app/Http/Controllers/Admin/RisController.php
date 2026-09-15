<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RisRequest;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Services\RisService;
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

    public function process(Request $request, $id, RisService $risService)
    {
        $ris = RisRequest::with('items')->findOrFail($id);
        $new_status = $request->new_status; 
        
        if ($new_status == 'Approved' && $ris->status != 'Approved') {
            foreach ($ris->items as $item) {
                if (strtolower((string) $item->stock_avail) === 'no') continue;

                $issueQty = !empty($item->issue_quantity) && $item->issue_quantity > 0 
                            ? (float) $item->issue_quantity 
                            : (float) $item->req_quantity;

                if ($issueQty > 0) {
                    $supply = $risService->resolveSupplyForItem($item);
                    if ($supply) {
                        $supply->decrement('quantity', $issueQty); 
                        Transaction::create([
                            'item_id' => $supply->id,
                            'item_type' => 'supplies',
                            'transaction_type' => 'OUT',
                            'quantity' => $issueQty,
                            'supplier' => $supply->supplier,
                            // Stock card "Office" column: the requesting office
                            // exactly as written on the RIS (not the division).
                            'office' => $ris->office,
                            'transaction_date' => now()->toDateString(),
                            'remarks' => 'RIS Auto-Release: ' . $ris->ris_no
                        ]);
                    }
                }
            }
        }

        if ($new_status == 'Pending Staff Review' && $ris->status == 'Approved') {
            foreach ($ris->items as $item) {
                if (strtolower((string) $item->stock_avail) === 'no') continue;

                $issueQty = !empty($item->issue_quantity) && $item->issue_quantity > 0 
                            ? (float) $item->issue_quantity 
                            : (float) $item->req_quantity;

                if ($issueQty > 0) {
                    $supply = $risService->resolveSupplyForItem($item);
                    if ($supply) {
                        $supply->increment('quantity', $issueQty); 
                        Transaction::create([
                            'item_id' => $supply->id,
                            'item_type' => 'supplies',
                            'transaction_type' => 'IN',
                            'quantity' => $issueQty,
                            'supplier' => $supply->supplier,
                            'office' => $ris->office,
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

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Processed RIS Request {$ris->ris_no} to status: {$new_status}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $msg = "RIS successfully updated to " . strtolower($new_status) . "!";
        return redirect('/admin/ris')->with('msg', $msg);
    }
}