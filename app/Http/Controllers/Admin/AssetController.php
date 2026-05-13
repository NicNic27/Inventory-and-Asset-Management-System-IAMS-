<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Transaction;
use App\Models\PurchaseOrderItem; 
use App\Models\IcsRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter', 'All');

        $query = Asset::query();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('article', 'LIKE', "%{$search}%")
                  ->orWhere('barcode_id', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($statusFilter !== 'All') {
            $query->where('status', $statusFilter);
        }

        $assets = $query->orderBy('id', 'desc')->paginate($perPage);

        $assets->getCollection()->transform(function ($asset) {
            $latestReq = IcsRequest::where('items_json', 'LIKE', '%"inv_no":"'.$asset->barcode_id.'"%')
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($latestReq) {
                $items = is_string($latestReq->items_json) ? json_decode($latestReq->items_json, true) : $latestReq->items_json;
                $asset_item = collect($items)->firstWhere('inv_no', $asset->barcode_id);
                $status = $asset_item['transfer_status'] ?? 'Active';
                
                $asset->assigned_to = ($status === 'Active') ? $latestReq->sig_received_by_name : null;
            } else {
                $asset->assigned_to = null;
            }
            return $asset;
        });

        $deliveredPoItems = collect();
        if (class_exists(PurchaseOrderItem::class)) {
            $existingAssetDescriptions = Asset::pluck('description')->map(function($desc) {
                return strtolower(trim($desc));
            });

            $rawPoItems = PurchaseOrderItem::with('purchaseOrder')
                ->whereHas('purchaseOrder', function($q) {
                    $q->where('po_type', 'Asset'); 
                })
                ->where('is_delivered', true)
                ->get();

            $deliveredPoItems = $rawPoItems->reject(function($item) use ($existingAssetDescriptions) {
                return in_array(strtolower(trim($item->description)), $existingAssetDescriptions->toArray());
            });
        }

        return view('admin.assets.index', compact('assets', 'perPage', 'deliveredPoItems'));
    }

    public function store(Request $request)
    {
        $duplicate = Asset::where('barcode_id', trim($request->barcode_id))->first();

        if ($duplicate) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'duplicate'
                ]);
            }
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->article) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/assets'), $imageName);
        }

        $asset = Asset::create([
            'barcode_id' => $request->barcode_id,
            'article' => $request->article,
            'description' => $request->description,
            'unit_measure' => $request->unit_measure,
            'supplier' => $request->supplier,
            'unit_value' => $request->unit_value,
            'status' => $request->status ?? 'Serviceable',
            'image' => $imageName
        ]);

        Transaction::create([
            'item_id' => $asset->id,
            'item_type' => 'assets',
            'transaction_type' => 'ADDED',
            'quantity' => 1, 
            'supplier' => $request->supplier,
            'transaction_date' => date('Y-m-d'),
            'remarks' => 'Opening Balance / New Item',
            'date_time' => now()
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created',
            'description' => "Added new asset: {$asset->article}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return redirect('/admin/assets')->with('msg', 'saved');
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        
        $duplicate = Asset::where('barcode_id', trim($request->barcode_id))
                          ->where('id', '!=', $id)
                          ->first();

        if ($duplicate) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'duplicate'
                ]);
            }
        }

        $imageName = $asset->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->article) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/assets'), $imageName);
        }

        $asset->update([
            'barcode_id' => $request->barcode_id,
            'article' => $request->article,
            'description' => $request->description,
            'unit_measure' => $request->unit_measure,
            'supplier' => $request->supplier,
            'unit_value' => $request->unit_value,
            'status' => $request->status,
            'image' => $imageName
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Updated asset details: {$asset->article}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return redirect('/admin/assets')->with('msg', 'saved');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted',
            'description' => "Deleted asset: {$asset->article}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $asset->delete();
        Transaction::where('item_id', $id)->where('item_type', 'assets')->delete();

        return redirect('/admin/assets')->with('msg', 'deleted');
    }

    public function details($id)
    {
        $asset = Asset::findOrFail($id);
        
        $allAssignments = IcsRequest::where('items_json', 'LIKE', '%"inv_no":"'.$asset->barcode_id.'"%')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $activeAssignment = null;
        $previousOwner = '<span class="text-muted">None</span>';
        $assignedTo = '<span class="text-muted">None</span>';
        $dateOfInventory = '<span class="text-muted">N/A</span>';

        $foundCurrentState = false;

        foreach ($allAssignments as $req) {
            $items = is_string($req->items_json) ? json_decode($req->items_json, true) : $req->items_json;
            $asset_item = collect($items)->firstWhere('inv_no', $asset->barcode_id);
            if (!$asset_item) continue;

            $transferStatus = $asset_item['transfer_status'] ?? 'Active';

            if (!$foundCurrentState) {
                $foundCurrentState = true;
                if ($transferStatus === 'Active') {
                    $activeAssignment = $req;
                    $assignedTo = $req->sig_received_by_name ?: 'Unknown';
                    $dateOfInventory = $asset_item['est_life'] ?? $req->created_at->format('M d, Y');
                } else {
                    $previousOwner = $req->sig_received_by_name ?: 'Unknown';
                }
            } else {
                if ($previousOwner === '<span class="text-muted">None</span>') {
                    $previousOwner = $req->sig_received_by_name ?: 'Unknown';
                }
            }
        }

        $imageHtml = $asset->image 
            ? '<img src="'.asset('storage/assets/'.$asset->image).'" class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: cover;">'
            : '<div class="bg-light rounded d-flex align-items-center justify-content-center border" style="height: 200px;"><i class="fas fa-laptop fa-4x text-muted opacity-25"></i></div>';
            
        if ($asset->status != 'Serviceable') {
            $statusBadge = '<span class="badge bg-danger px-3 py-2 fs-6">'.$asset->status.'</span>';
        } elseif ($activeAssignment) {
            $statusBadge = '<span class="badge px-3 py-2 fs-6 shadow-sm" style="background-color: #101954; color: white;"><i class="fas fa-user-check me-1"></i> Assigned</span>';
        } else {
            $statusBadge = '<span class="badge bg-success px-3 py-2 fs-6 shadow-sm"><i class="fas fa-check-circle me-1"></i> Available</span>';
        }

        $html = '
        <div class="modal-header bg-primary text-white border-0">
            <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i> Asset Details</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
            <div class="row align-items-center mb-4">
                <div class="col-md-5 text-center mb-3 mb-md-0">
                    '.$imageHtml.'
                </div>
                <div class="col-md-7">
                    <h3 class="fw-bold text-dark mb-1">'.$asset->article.'</h3>
                    <p class="text-muted mb-3">'.$asset->description.'</p>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        '.$statusBadge.'
                    </div>
                </div>
            </div>
            
            <div class="bg-light p-3 rounded text-center border mb-3">
                <span class="text-muted d-block small fw-bold text-uppercase mb-2">Property No. (Barcode ID)</span>
                <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text='.urlencode($asset->barcode_id).'&scale=3&height=10&includetext=false" style="max-height: 50px; max-width: 100%; mix-blend-mode: multiply;">
                <div class="font-monospace fw-bold mt-1 fs-5" style="letter-spacing: 2px; color: #101954;">'.$asset->barcode_id.'</div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="border rounded p-3 bg-white h-100 shadow-sm border-start border-4 border-primary">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Person Accountable</small>
                        <span class="fw-semibold text-dark">'.$assignedTo.'</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-3 bg-white h-100 shadow-sm border-start border-4 border-secondary">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Previous Holder</small>
                        <span class="fw-semibold text-dark">'.$previousOwner.'</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-3 bg-white h-100 shadow-sm border-start border-4 border-warning">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Date of Inventory</small>
                        <span class="fw-semibold text-dark">'.$dateOfInventory.'</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-3 bg-white h-100 shadow-sm">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Supplier</small>
                        <span class="fw-semibold text-dark">'.($asset->supplier ?: 'N/A').'</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3 bg-white h-100 shadow-sm">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Unit Measure</small>
                        <span class="fw-semibold text-dark">'.($asset->unit_measure ?: 'N/A').'</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3 bg-light shadow-sm d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Unit Value</small>
                        <span class="fw-bold text-success fs-4">₱ '.number_format($asset->unit_value, 2).'</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-light border-0">
            <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Close</button>
        </div>';

        return response($html);
    }

    public function updateScanStatus(Request $request)
    {
        $asset = Asset::where('barcode_id', $request->barcode_id)->first();

        if (!$asset) {
            return response()->json(['status' => 'error', 'message' => 'Asset not found. Please check the barcode.']);
        }

        $newStatus = $request->status; 
        $asset->update(['status' => $newStatus]);

        $txType = $newStatus == 'Serviceable' ? 'IN' : 'OUT';
        $remarks = $newStatus == 'Serviceable' ? 'Returned (Serviceable)' : 'Marked as Defective/Unserviceable';

        if ($newStatus == 'Serviceable') {
            $activeAssignment = IcsRequest::where('items_json', 'LIKE', '%"inv_no":"'.$asset->barcode_id.'"%')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($activeAssignment) {
                $itemsArray = is_string($activeAssignment->items_json) ? json_decode($activeAssignment->items_json, true) : $activeAssignment->items_json;
                $asset_item = collect($itemsArray)->firstWhere('inv_no', $asset->barcode_id);
                
                if (($asset_item['transfer_status'] ?? 'Active') === 'Active') {
                    foreach ($itemsArray as &$item) {
                        if (isset($item['inv_no']) && $item['inv_no'] == $asset->barcode_id) {
                            $item['transfer_status'] = 'Returned to Inventory';
                        }
                    }
                    $activeAssignment->update(['items_json' => $itemsArray]);
                }
            }
        }

        Transaction::create([
            'item_id' => $asset->id,
            'item_type' => 'assets',
            'transaction_type' => $txType,
            'quantity' => 1, 
            'transaction_date' => date('Y-m-d'),
            'remarks' => $remarks,
            'date_time' => now()
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Scanner updated asset status: {$asset->article} to {$newStatus}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "{$asset->article} has been updated to {$newStatus}.",
            'asset_name' => $asset->article,
            'new_state' => $newStatus
        ]);
    }
}