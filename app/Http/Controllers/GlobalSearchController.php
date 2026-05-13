<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Asset;
use App\Models\Supply;
use App\Models\IcsRequest; // Included ICS model

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->get('q'));
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();
        if (!$user) return response()->json([]);

        $role = trim(strtolower($user->role)); 
        $results = [];
        $searchTerm = '%' . $query . '%';
        
        // Lock calculation to 10 items per page for perfect accuracy (or adjust to match your frontend default)
        $perPage = 10; 

        // 1. SEARCH INVENTORY & TRANSACTIONS (Staff Only)
        if ($role === 'staff' || $role === 'admin') {
            
            // --- Search Supplies ---
            try {
                $supplies = Supply::where('article', 'LIKE', $searchTerm)
                    ->orWhere('barcode_id', 'LIKE', $searchTerm)
                    ->orWhere('description', 'LIKE', $searchTerm)
                    ->limit(4)->get();
                    
                foreach ($supplies as $item) {
                    $position = Supply::where('id', '>=', $item->id)->count();
                    $page = ceil($position / $perPage) ?: 1;

                    $results[] = [
                        'type' => 'Supply',
                        'title' => $item->article,
                        'meta' => 'Barcode: ' . ($item->barcode_id ?? 'N/A'),
                        'url' => url('/supplies?search=' . urlencode($item->barcode_id))
                    ];
                }
            } catch (\Exception $e) {}

            // --- Search Assets ---
            try {
                $assets = Asset::where('article', 'LIKE', $searchTerm)
                    ->orWhere('barcode_id', 'LIKE', $searchTerm)
                    ->orWhere('description', 'LIKE', $searchTerm)
                    ->limit(4)->get();
                    
                foreach ($assets as $item) {
                    $position = Asset::where('id', '>=', $item->id)->count();
                    $page = ceil($position / $perPage) ?: 1;

                    $results[] = [
                        'type' => 'Asset',
                        'title' => $item->article,
                        'meta' => 'Property No: ' . ($item->barcode_id ?? 'N/A'),
                        // Redirect to the list and pre-filter it
                        'url' => url('/asset-list?search=' . urlencode($item->barcode_id)) 
                    ];
                }
            } catch (\Exception $e) {}
            
            // --- Search ICS / PAR History ---
            try {
                if (class_exists(IcsRequest::class)) {
                    $icsRequests = IcsRequest::where('ics_no', 'LIKE', $searchTerm)
                        ->orWhere('sig_received_by_name', 'LIKE', $searchTerm)
                        ->orWhere('sig_received_from_name', 'LIKE', $searchTerm)
                        ->limit(4)->get();
                        
                    foreach ($icsRequests as $ics) {
                        // For pagination logic if you are relying on IDs descending
                        $position = IcsRequest::where('id', '>=', $ics->id)->count();
                        $page = ceil($position / $perPage) ?: 1;

                        $results[] = [
                            'type' => 'Document (' . $ics->category . ')',
                            'title' => $ics->ics_no,
                            'meta' => 'Assigned To: ' . ($ics->sig_received_by_name ?: 'N/A'),
                            'url' => url('/ics/history?search=' . urlencode($ics->ics_no))
                        ];
                    }
                }
            } catch (\Exception $e) {}

            // --- Search Transactions ---
            try {
                if (Schema::hasTable('transactions')) {
                    $transactions = DB::table('transactions')
                        ->where('remarks', 'LIKE', $searchTerm)
                        ->orWhere('transaction_type', 'LIKE', $searchTerm)
                        ->limit(3)->get();
                        
                    foreach ($transactions as $tx) {
                        $position = DB::table('transactions')->where('id', '>=', $tx->id)->count();
                        $page = ceil($position / $perPage) ?: 1;

                        $results[] = [
                            'type' => 'Transaction',
                            'title' => strtoupper($tx->transaction_type) . ' - Qty: ' . $tx->quantity,
                            'meta' => 'Remarks: ' . $tx->remarks,
                            'url' => url('/transactions?page=' . $page . '&per_page=' . $perPage)
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // 2. BULLETPROOF RIS SEARCH (Staff, Admins, & Users)
        try {
            $risTable = null;
            if (Schema::hasTable('requests')) $risTable = 'requests';
            elseif (Schema::hasTable('ris')) $risTable = 'ris';
            elseif (Schema::hasTable('ris_requests')) $risTable = 'ris_requests';

            if ($risTable) {
                $columns = Schema::getColumnListing($risTable);
                $risCol = in_array('ris_no', $columns) ? 'ris_no' : (in_array('ris_number', $columns) ? 'ris_number' : 'id');
                $purposeCol = in_array('purpose', $columns) ? 'purpose' : (in_array('remarks', $columns) ? 'remarks' : null);

                $risQuery = DB::table($risTable)->where(function($q) use ($searchTerm, $risCol, $purposeCol) {
                    $q->where($risCol, 'LIKE', $searchTerm);
                    if ($purposeCol) {
                        $q->orWhere($purposeCol, 'LIKE', $searchTerm);
                    }
                });

                if ($role === 'frontuser' && in_array('user_id', $columns)) {
                    $risQuery->where('user_id', $user->id);
                }

                $requests = $risQuery->limit(4)->get();
                
                foreach ($requests as $req) {
                    $prefix = ($role === 'frontuser') ? '/user/ris' : '/ris';
                    
                    $position = DB::table($risTable)->where('id', '>=', $req->id)->count();
                    $page = ceil($position / $perPage) ?: 1;

                    $val = $req->{$risCol} ?? 'Pending Request';
                    $purp = $purposeCol ? ($req->{$purposeCol} ?? 'No purpose defined') : 'Request details';

                    $results[] = [
                        'type' => 'RIS Request',
                        'title' => 'RIS: ' . $val,
                        'meta' => 'Purpose: ' . substr($purp, 0, 35) . '...',
                        'url' => url($prefix . '?page=' . $page . '&per_page=' . $perPage)
                    ];
                }
            }
        } catch (\Exception $e) {}

        return response()->json($results);
    }
}