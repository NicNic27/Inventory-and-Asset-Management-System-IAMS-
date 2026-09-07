<?php

namespace App\Http\Controllers;

use App\Models\RisRequest;
use App\Models\RisItem;
use App\Models\Supply; 
use App\Models\Transaction;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Services\PrReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RisController extends Controller
{
    public function create()
    {
        $seqSetting = SystemSetting::firstOrCreate(
            ['key' => 'seq_ris_no'], 
            ['value' => '1']
        );
        $risNumber = 'RIS-' . date('Y-m') . '-' . str_pad($seqSetting->value, 4, '0', STR_PAD_LEFT);
        $supplies = Supply::orderBy('article', 'asc')->get();
        return view('ris.create', compact('risNumber', 'supplies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $seqSetting = SystemSetting::firstOrCreate(
            ['key' => 'seq_ris_no'], 
            ['value' => '1']
        );

        $currentNumber = (int) $seqSetting->value;
        $yearMonth = date('Y-m'); 
        $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT); 
        $generatedRisNo = 'RIS-' . $yearMonth . '-' . $sequenceFormatted;

        while (RisRequest::where('ris_no', $generatedRisNo)->exists()) {
            $currentNumber++;
            $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
            $generatedRisNo = 'RIS-' . $yearMonth . '-' . $sequenceFormatted;
        }

        $seqSetting->update(['value' => $currentNumber + 1]);

        $ris = new RisRequest();
        $ris->user_id = $user->id; 
        $ris->ris_no = $generatedRisNo; 
        $ris->entity_name = $request->entity_name;
        $ris->division = $request->division;
        $ris->office = $request->unit_section;
        $ris->fund_cluster = $request->fund_cluster;
        $ris->rcc = $request->center_code;
        $ris->purpose = is_array($request->purpose) ? implode('; ', array_filter(array_unique($request->purpose))) : $request->purpose;
        $ris->sig_requested_by = $request->requested_by;
        $ris->desig_requested = $request->desig_requested;
        $ris->date_requested = now()->toDateString();
        $ris->sig_approved_by = $request->approved_by;
        $ris->desig_approved = $request->desig_approved;
        $ris->sig_issued_by = $request->issued_by;
        $ris->desig_issued = $request->desig_issued;
        $ris->sig_received_by = $request->received_by;
        $ris->desig_received = $request->desig_received;
        
        $ris->status = 'Pending Staff Review'; 
        $ris->save();

        $itemCount = count($request->description ?? []);
        $itemsBySupply = [];
        for ($i = 0; $i < $itemCount; $i++) {
            $desc = $request->description[$i] ?? null;
            if ($desc === 'Others') {
                $desc = $request->manual_description[$i] ?? 'Unspecified Item';
            }

            if (!empty($desc)) {
                $unit = $request->unit_measure[$i] ?? '';
                $itemKey = strtolower(trim($desc)) . '|' . strtolower(trim($unit));
                if (!isset($itemsBySupply[$itemKey])) {
                    $itemsBySupply[$itemKey] = [
                        'stock_no' => $request->stock_no[$i] ?? null,
                        'unit' => $unit,
                        'description' => $desc,
                        'req_quantity' => 0,
                        'stock_avail' => 'N/A',
                        'remarks' => $request->remarks[$i] ?? null,
                    ];
                }
                $itemsBySupply[$itemKey]['req_quantity'] += (int) ($request->quantity[$i] ?? 0);
            }
        }

        foreach ($itemsBySupply as $itemData) {
            RisItem::create(['ris_id' => $ris->id] + $itemData);
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Created',
            'description' => "Staff created RIS from physical form: {$generatedRisNo}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/ris')->with('msg', 'RIS successfully created! RIS No. ' . $generatedRisNo);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = RisRequest::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ris_no', 'like', "%{$search}%")
                  ->orWhere('sig_requested_by', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_filter') && $request->status_filter !== 'All') {
            $query->where('status', $request->status_filter);
        }

        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'priority') {
            $query->orderByRaw("FIELD(status, 'Pending Staff Review', 'Forwarded to Admin', 'Approved', 'Declined', 'Rejected', 'Cancelled') asc")
                  ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $requests = $query->paginate($perPage);
                              
        return view('ris.index', compact('requests', 'perPage'));
    }

    public function review($id)
    {
        $req = RisRequest::with('items')->findOrFail($id);
        
        foreach ($req->items as $item) {
            $supply = Supply::where('barcode_id', $item->stock_no)->first();
            
            if ($supply) {
                // Compute actual on-hand from transactions (true source of truth)
                // instead of supply.quantity which can drift out of sync
                $totalIn = Transaction::where('item_id', $supply->id)
                    ->where('item_type', 'supplies')
                    ->whereIn('transaction_type', ['IN', 'ADDED', 'RETURNED'])
                    ->sum('quantity');
                $totalOut = Transaction::where('item_id', $supply->id)
                    ->where('item_type', 'supplies')
                    ->where('transaction_type', 'OUT')
                    ->sum('quantity');
                $item->current_stock = max(0, (int) $totalIn - (int) $totalOut);
            } else {
                $item->current_stock = 0;
            }
        }

        return view('ris.review', compact('req'));
    }

    public function update(Request $request, $id, PrReferralService $prReferralService)
    {
        $ris = RisRequest::findOrFail($id);

        $status = $ris->status; 
        $msg = 'updated'; 

        if ($request->action == 'forward') {
            $status = 'Forwarded to Admin';
            $msg = 'forwarded';
        } elseif ($request->action == 'return') {
            $status = 'Pending Staff Review'; 
            $msg = 'returned';
        } elseif ($request->action == 'redirect_procurement') {
            $status = 'Redirected to Procurement';
            $msg = 'redirected';
        } else {
            if ($ris->status != 'Approved') {
                $status = 'Pending Staff Review';
            }
        }

        $ris->update([
            'entity_name' => $request->entity_name,
            'division' => $request->division,
            'office' => $request->office,
            'fund_cluster' => $request->fund_cluster,
            'rcc' => $request->rcc,
            'purpose' => $request->purpose,
            
            'sig_requested_by' => $request->sig_requested_by,
            'sig_approved_by' => $request->sig_approved_by,
            'sig_issued_by' => $request->sig_issued_by,
            'sig_received_by' => $request->sig_received_by,
            
            'desig_requested' => $request->desig_requested,
            'desig_approved' => $request->desig_approved,
            'desig_issued' => $request->desig_issued,
            'desig_received' => $request->desig_received,
            
            'date_requested' => $request->date_requested ?: null,
            'date_approved' => $request->date_approved ?: null,
            'date_issued' => $request->date_issued ?: null,
            'date_received' => $request->date_received ?: null,
            
            'status' => $status
        ]);

        if ($request->has('item_id')) {
            $itemCount = count($request->item_id);
            for ($i = 0; $i < $itemCount; $i++) {
                $itemId = $request->item_id[$i] ?? 0;
                $stockNo = $request->stock_no[$i] ?? null;
                $desc = $request->description[$i] ?? null;
                
                $avail = $request->input("stock_avail_$i") ?? 'N/A';

                if (!empty($stockNo) || !empty($desc)) {
                    $itemData = [
                        'ris_id' => $ris->id,
                        'stock_no' => $stockNo,
                        'unit' => $request->unit[$i] ?? null,
                        'description' => $desc,
                        'req_quantity' => $request->req_quantity[$i] ?? null,
                        'stock_avail' => $avail,
                        'issue_quantity' => $request->issue_quantity[$i] ?? null,
                        'remarks' => $request->remarks[$i] ?? null,
                    ];

                    if ($itemId > 0) {
                        RisItem::where('id', $itemId)->update($itemData);
                        $savedItem = RisItem::find($itemId);
                    } else {
                        $savedItem = RisItem::create($itemData);
                    }

                    // No stock on hand: refer this line item to BAC for procurement
                    if (strtolower((string) $avail) === 'no' && $savedItem) {
                        $supply = Supply::where('barcode_id', $stockNo)->first();
                        $alreadyReferred = \App\Models\PrReferral::where('ris_item_id', $savedItem->id)
                            ->whereIn('status', ['referred', 'po_issued'])
                            ->exists();

                        if ($supply && !$alreadyReferred) {
                            $prReferralService->createReferral([
                                'ris_id' => $ris->id,
                                'ris_item_id' => $savedItem->id,
                                'supply_id' => $supply->id,
                                'quantity_needed' => $request->req_quantity[$i] ?? 1,
                            ]);
                        }
                    }
                }
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Processed/Updated RIS: {$ris->ris_no} (Status: {$status})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/ris')->with('msg', $msg);
    }
}