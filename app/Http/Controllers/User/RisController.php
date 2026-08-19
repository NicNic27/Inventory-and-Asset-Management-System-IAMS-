<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RisRequest;
use App\Models\RisItem;
use App\Models\Supply; 
use App\Models\SystemSetting; 
use App\Models\ActivityLog;
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
        return view('user.ris.create', compact('risNumber', 'supplies'));
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
        $ris->division = $request->unit_section;
        $ris->office = $request->office;
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
            'description' => "User submitted a new RIS request: {$generatedRisNo}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/user/ris/history')->with('msg', 'RIS Request successfully submitted! Your assigned RIS No. is ' . $generatedRisNo);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $firstName = trim($user->firstname);

        $query = RisRequest::with('items') 
            ->where(function($q) use ($user, $firstName) {
                $q->where('user_id', $user->id)
                  ->orWhere('sig_requested_by', 'LIKE', "%{$firstName}%");
            });

        if ($request->filled('search')) {
            $query->where('ris_no', 'like', '%' . trim($request->search) . '%');
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('status')) {
            $status = strtolower($request->status);
            if ($status == 'approved') {
                $query->where('status', 'Approved');
            } elseif ($status == 'pending') {
                $query->whereIn('status', ['Pending Staff Review', 'Forwarded to Admin']); 
            } elseif ($status == 'declined') {
                $query->whereIn('status', ['Declined', 'Cancelled', 'Rejected']);
            }
        }

        $perPage = $request->input('per_page', 10); 
        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('user.ris.history', compact('requests', 'perPage'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $firstName = trim($user->firstname);

        $req = RisRequest::with('items')
            ->where('id', $id)
            ->where(function($q) use ($user, $firstName) {
                $q->where('user_id', $user->id)
                  ->orWhere('sig_requested_by', 'LIKE', "%{$firstName}%");
            })
            ->firstOrFail();

        return view('user.ris.show', compact('req'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $firstName = trim($user->firstname);

        $req = RisRequest::with('items')
            ->where('id', $id)
            ->where(function($q) use ($user, $firstName) {
                $q->where('user_id', $user->id)
                  ->orWhere('sig_requested_by', 'LIKE', "%{$firstName}%");
            })
            ->firstOrFail();

        if ($req->status != 'Pending Staff Review') {
            return redirect('/user/ris/history')->with('msg', 'This request can no longer be edited as it is already being processed.');
        }

        $supplies = Supply::orderBy('article', 'asc')->get();
        return view('user.ris.edit', compact('req', 'supplies'));
    }

    public function update(Request $request, $id)
    {
        $ris = RisRequest::findOrFail($id);

        if ($ris->status != 'Pending Staff Review') {
            return redirect('/user/ris/history')->with('msg', 'This request can no longer be edited.');
        }

        $ris->update([
            'office' => $request->office,
            'division' => $request->unit_section ?? $ris->division,
            'fund_cluster' => $request->fund_cluster,
            'rcc' => $request->center_code,
            'purpose' => is_array($request->purpose) ? implode('; ', array_filter(array_unique($request->purpose))) : $request->purpose,
        ]);

        RisItem::where('ris_id', $ris->id)->delete();

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
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "User modified their pending RIS request: {$ris->ris_no}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/user/ris/history')->with('msg', 'RIS Request successfully updated!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $firstName = trim($user->firstname);

        $ris = RisRequest::where('id', $id)
            ->where(function($q) use ($user, $firstName) {
                $q->where('user_id', $user->id)
                  ->orWhere('sig_requested_by', 'LIKE', "%{$firstName}%");
            })
            ->firstOrFail();

        if ($ris->status != 'Pending Staff Review') {
            return redirect('/user/ris/history')->with('error', 'You cannot delete a request that is already being processed.');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted',
            'description' => "User cancelled/deleted their RIS request: {$ris->ris_no}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        RisItem::where('ris_id', $ris->id)->delete();
        $ris->delete();

        return redirect('/user/ris/history')->with('msg', 'RIS Request successfully deleted.');
    }
}