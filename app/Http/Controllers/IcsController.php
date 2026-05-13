<?php

namespace App\Http\Controllers;

use App\Models\IcsRequest;
use App\Models\SystemSetting; 
use App\Models\Asset;
use App\Models\ActivityLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class IcsController extends Controller
{
    public function create()
    {
        $yearMonth = date('Y-m');

        $seqPar = SystemSetting::firstOrCreate(['key' => 'seq_par_no'], ['value' => '1'])->value;
        $seqSphv = SystemSetting::firstOrCreate(['key' => 'seq_sphv_no'], ['value' => '1'])->value;
        $seqSplv = SystemSetting::firstOrCreate(['key' => 'seq_splv_no'], ['value' => '1'])->value;

        $parNumber = 'PAR-' . $yearMonth . '-' . str_pad($seqPar, 4, '0', STR_PAD_LEFT);
        $sphvNumber = 'SPHV-' . $yearMonth . '-' . str_pad($seqSphv, 4, '0', STR_PAD_LEFT);
        $splvNumber = 'SPLV-' . $yearMonth . '-' . str_pad($seqSplv, 4, '0', STR_PAD_LEFT);

        $assets = Asset::orderBy('article', 'asc')->get();

        return view('ics.create', compact('parNumber', 'sphvNumber', 'splvNumber', 'assets'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasColumn('ics_requests', 'po_type')) {
            Schema::table('ics_requests', function (Blueprint $table) {
                $table->string('po_type')->nullable();
                $table->string('po_no')->nullable();
                $table->date('po_date')->nullable();
            });
        }

        $category = $request->item_category ?? 'Low - Valued';
        $yearMonth = date('Y-m');

        if ($category === 'PPE') {
            $settingKey = 'seq_par_no';
            $prefix = 'PAR-';
        } elseif ($category === 'High - Valued') {
            $settingKey = 'seq_sphv_no';
            $prefix = 'SPHV-';
        } else {
            $settingKey = 'seq_splv_no';
            $prefix = 'SPLV-';
        }

        $seqSetting = SystemSetting::firstOrCreate(['key' => $settingKey], ['value' => '1']);
        $currentNumber = (int) $seqSetting->value;
        $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
        $generatedNo = $prefix . $yearMonth . '-' . $sequenceFormatted;

        while (IcsRequest::where('ics_no', $generatedNo)->exists()) {
            $currentNumber++;
            $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
            $generatedNo = $prefix . $yearMonth . '-' . $sequenceFormatted;
        }

        $seqSetting->update(['value' => $currentNumber + 1]);

        $items = [];
        $itemCount = count($request->qty ?? []);
        
        for ($i = 0; $i < $itemCount; $i++) {
            if (!empty($request->qty[$i]) || !empty($request->desc[$i])) {
                $items[] = [
                    'qty' => $request->qty[$i],
                    'unit' => $request->unit[$i],
                    'article' => $request->article[$i] ?? 'Item',
                    'desc' => $request->desc[$i],
                    'specs' => $request->specs[$i] ?? '',
                    'inv_no' => $request->inv_no[$i],
                    'est_life' => $request->est_life[$i],
                    'unit_cost' => $request->unit_cost[$i] ?? null,
                    'total_cost' => $request->total_cost[$i],
                    'transfer_status' => 'Active' 
                ];
            }
        }

        IcsRequest::create([
            'ics_no' => $generatedNo,
            'fund_cluster' => $request->fund_cluster,
            'category' => $category,
            'po_type' => $request->po_type,
            'po_no' => $request->po_no,
            'po_date' => $request->po_date,
            'sig_received_from_name' => $request->sig_from_name,
            'sig_received_from_pos' => $request->sig_from_pos,
            'sig_from_date' => $request->sig_from_date,
            'sig_received_by_name' => $request->sig_by_name,
            'sig_received_by_pos' => $request->sig_by_pos,
            'sig_by_date' => $request->sig_by_date,
            'status' => 'Pending',
            'items_json' => $items,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created',
            'description' => "Generated new property document: {$generatedNo}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/ics/history')->with('msg', 'Equipment Request successfully submitted! Assigned ID: ' . $generatedNo);
    }
    
    public function history()
    {
        $requests = IcsRequest::orderBy('created_at', 'desc')->get();
        return view('ics.history', compact('requests'));
    }

    public function uploadSigned(Request $request, $id)
    {
        $ics = IcsRequest::findOrFail($id);
        
        $request->validate([
            'signed_document' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('signed_document')) {
            $file = $request->file('signed_document');
            $filename = time() . '_signed_ics_' . $id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/ics_signed'), $filename);
            
            $ics->signed_document = $filename;
            $ics->save();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated',
                'description' => "Uploaded signed document for: {$ics->ics_no}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        return back()->with('msg', 'Signed document uploaded successfully!');
    }

    public function sticker($id, $itemIndex)
    {
        $ics = IcsRequest::findOrFail($id);
        $items = $ics->items_json;
        $item = $items[$itemIndex] ?? null;

        if (!$item) {
            return redirect('/ics/history')->with('error', 'Item not found.');
        }

        return view('ics.sticker', compact('ics', 'item'));
    }
    
    public function viewDigital($id)
    {
        $ics = IcsRequest::findOrFail($id);
        return response()->json($ics);
    }

    public function edit($id)
    {
        $ics = IcsRequest::findOrFail($id);
        return response()->json($ics);
    }

    public function update(Request $request, $id)
    {
        $ics = IcsRequest::findOrFail($id);
        
        if (!Schema::hasColumn('ics_requests', 'po_type')) {
            Schema::table('ics_requests', function (Blueprint $table) {
                $table->string('po_type')->nullable();
                $table->string('po_no')->nullable();
                $table->date('po_date')->nullable();
            });
        }

        $items = [];
        $itemCount = count($request->qty ?? []);
        
        $oldItems = is_string($ics->items_json) ? json_decode($ics->items_json, true) : $ics->items_json;

        for ($i = 0; $i < $itemCount; $i++) {
            if (!empty($request->qty[$i]) || !empty($request->desc[$i])) {
                $items[] = [
                    'qty' => $request->qty[$i],
                    'unit' => $request->unit[$i],
                    'article' => $request->article[$i] ?? 'Item',
                    'desc' => $request->desc[$i],
                    'specs' => $request->specs[$i] ?? '',
                    'inv_no' => $request->inv_no[$i],
                    'est_life' => $request->est_life[$i],
                    'unit_cost' => $request->unit_cost[$i] ?? null,
                    'total_cost' => $request->total_cost[$i],
                    'transfer_status' => $oldItems[$i]['transfer_status'] ?? 'Active'
                ];
            }
        }

        $ics->update([
            'fund_cluster' => $request->fund_cluster,
            'po_type' => $request->po_type,
            'po_no' => $request->po_no,
            'po_date' => $request->po_date,
            'sig_received_from_name' => $request->sig_from_name,
            'sig_received_from_pos' => $request->sig_from_pos,
            'sig_received_by_name' => $request->sig_by_name,
            'sig_received_by_pos' => $request->sig_by_pos,
            'items_json' => $items,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Updated property document details: {$ics->ics_no}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('msg', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        $ics = IcsRequest::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted',
            'description' => "Deleted property document: {$ics->ics_no}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $ics->delete();
        return back()->with('msg', 'Document deleted successfully!');
    }

    public function transferItem(Request $request, $id, $itemIndex)
    {
        $oldIcs = IcsRequest::findOrFail($id);
        $items = is_string($oldIcs->items_json) ? json_decode($oldIcs->items_json, true) : $oldIcs->items_json;
        $itemToTransfer = $items[$itemIndex] ?? null;

        if (!$itemToTransfer) return back()->with('error', 'Item not found.');

        DB::beginTransaction();
        try {
            $category = $oldIcs->category;
            $yearMonth = date('Y-m');

            if ($category === 'PPE') {
                $settingKey = 'seq_par_no';
                $prefix = 'PAR-';
            } elseif ($category === 'High - Valued') {
                $settingKey = 'seq_sphv_no';
                $prefix = 'SPHV-';
            } else {
                $settingKey = 'seq_splv_no';
                $prefix = 'SPLV-';
            }

            $seqSetting = SystemSetting::firstOrCreate(['key' => $settingKey], ['value' => '1']);
            $currentNumber = (int) $seqSetting->value;
            $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
            $generatedNo = $prefix . $yearMonth . '-' . $sequenceFormatted;

            while (IcsRequest::where('ics_no', $generatedNo)->exists()) {
                $currentNumber++;
                $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
                $generatedNo = $prefix . $yearMonth . '-' . $sequenceFormatted;
            }

            $seqSetting->update(['value' => $currentNumber + 1]);

            $itemToTransfer['transfer_status'] = 'Active'; 
            
            $prevAccountable = $oldIcs->category == 'PPE' ? $oldIcs->sig_received_from_name : $oldIcs->sig_received_by_name;
            $prevPosition = $oldIcs->category == 'PPE' ? $oldIcs->sig_received_from_pos : $oldIcs->sig_received_by_pos;
            
            if ($category === 'PPE') {
                $sigFromName = $request->new_accountable_person; 
                $sigFromPos = $request->new_position;
                $sigByName = $prevAccountable ?: 'System / Previous Holder'; 
                $sigByPos = $prevPosition;
            } else {
                $sigFromName = $prevAccountable ?: 'System / Previous Holder'; 
                $sigFromPos = $prevPosition;
                $sigByName = $request->new_accountable_person; 
                $sigByPos = $request->new_position;
            }

            $newIcs = IcsRequest::create([
                'ics_no' => $generatedNo,
                'fund_cluster' => $oldIcs->fund_cluster,
                'category' => $category,
                'po_type' => $oldIcs->po_type,
                'po_no' => $oldIcs->po_no,
                'po_date' => $oldIcs->po_date,
                'sig_received_from_name' => $sigFromName,
                'sig_received_from_pos' => $sigFromPos,
                'sig_from_date' => now()->toDateString(),
                'sig_received_by_name' => $sigByName,
                'sig_received_by_pos' => $sigByPos,
                'sig_by_date' => $request->transfer_date,
                'status' => 'Pending',
                'items_json' => [$itemToTransfer], 
            ]);

            $items[$itemIndex]['transfer_status'] = "Transferred to {$generatedNo}";
            $oldIcs->update(['items_json' => $items]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created',
                'description' => "Transferred item {$itemToTransfer['inv_no']} from {$oldIcs->ics_no} to new document {$generatedNo}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            if (!empty($itemToTransfer['inv_no'])) {
                $asset = Asset::where('barcode_id', $itemToTransfer['inv_no'])->first();
                if ($asset) {
                    Transaction::create([
                        'item_id' => $asset->id,
                        'item_type' => 'assets',
                        'transaction_type' => 'OUT',
                        'quantity' => 1,
                        'supplier' => $asset->supplier,
                        'transaction_date' => now()->toDateString(),
                        'remarks' => "Transferred via {$generatedNo} to {$request->new_accountable_person}",
                        'date_time' => now()
                    ]);
                }
            }

            DB::commit();
            return back()->with('msg', "Item successfully transferred! New Document: {$generatedNo} was generated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}