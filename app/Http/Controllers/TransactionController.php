<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        // Increase the group_concat max length to prevent truncation 
        // if an RIS has a massive amount of items in it.
        DB::statement('SET SESSION group_concat_max_len = 100000');

        $transactions = DB::table('transactions as t')
            ->leftJoin('assets as a', function($join) {
                $join->on('t.item_id', '=', 'a.id')
                     ->where('t.item_type', '=', 'assets');
            })
            ->leftJoin('supplies as s', function($join) {
                $join->on('t.item_id', '=', 's.id')
                     ->where('t.item_type', '=', 'supplies');
            })
            ->leftJoin('ris_requests as r', function($join) {
                $join->whereRaw("t.remarks LIKE CONCAT('%', r.ris_no, '%')");
            })
            // Ignore any transaction where quantity is 0 or less
            ->where('t.quantity', '>', 0)
            ->select(
                DB::raw("IF(t.transaction_type = 'OUT' AND t.remarks LIKE 'RIS%', t.remarks, CAST(t.id AS CHAR)) as group_key"),
                DB::raw("MAX(t.date_time) as date_time"),
                DB::raw("MAX(t.transaction_type) as transaction_type"),
                DB::raw("SUM(t.quantity) as quantity"),
                DB::raw("MAX(t.remarks) as remarks"),
                DB::raw("MAX(COALESCE(a.article, s.article)) as item_name"),
                DB::raw("MAX(COALESCE(a.barcode_id, s.barcode_id)) as item_code"),
                DB::raw("MAX(t.item_type) as raw_item_type"),
                DB::raw("MAX(COALESCE(a.supplier, s.supplier)) as supplier"),
                DB::raw("MAX(r.sig_requested_by) as requested_by"), 
                
                // Bundle all grouped item details into separated strings
                DB::raw("GROUP_CONCAT(COALESCE(a.article, s.article) SEPARATOR '||') as grouped_items"),
                DB::raw("GROUP_CONCAT(COALESCE(a.barcode_id, s.barcode_id) SEPARATOR '||') as grouped_codes"),
                DB::raw("GROUP_CONCAT(t.quantity SEPARATOR '||') as grouped_qtys"),
                
                // NEW: Subqueries inside group_concat to grab current qty & total input qty for the 47/51 display
                DB::raw("GROUP_CONCAT(
                    CONCAT(
                        COALESCE(s.quantity, 0), 
                        ' / ', 
                        GREATEST(COALESCE(s.quantity, 0), (SELECT COALESCE(SUM(quantity), 0) FROM transactions WHERE item_id = s.id AND item_type = 'supplies' AND transaction_type IN ('IN', 'Added')))
                    ) SEPARATOR '||'
                ) as grouped_totals"),
                
                DB::raw("COUNT(t.id) as item_count")
            )
            ->groupBy('group_key')
            ->orderByRaw('MAX(t.date_time) DESC')
            ->paginate($perPage);

        return view('transactions.index', compact('transactions', 'perPage'));
    }
}