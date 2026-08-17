<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Get all transactions with complex grouping logic
     * Supports pagination and filtering
     */
    public function getAllTransactions(int $perPage = 15, array $filters = [])
    {
        // Increase group_concat to prevent truncation
        DB::statement('SET SESSION group_concat_max_len = 100000');

        $query = DB::table('transactions as t')
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
            ->where('t.quantity', '>', 0);

        // Apply filters if provided
        if (!empty($filters['transaction_type'])) {
            $query->where('t.transaction_type', $filters['transaction_type']);
        }

        if (!empty($filters['item_type'])) {
            $query->where('t.item_type', $filters['item_type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('a.article', 'LIKE', "%{$search}%")
                  ->orWhere('s.article', 'LIKE', "%{$search}%")
                  ->orWhere('t.remarks', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('t.date_time', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('t.date_time', '<=', $filters['date_to']);
        }

        return $query->select(
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
            DB::raw("GROUP_CONCAT(COALESCE(a.article, s.article) SEPARATOR '||') as grouped_items"),
            DB::raw("GROUP_CONCAT(COALESCE(a.barcode_id, s.barcode_id) SEPARATOR '||') as grouped_codes"),
            DB::raw("GROUP_CONCAT(t.quantity SEPARATOR '||') as grouped_qtys"),
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
    }

    /**
     * Get transaction by ID
     */
    public function getTransactionById(int $id)
    {
        return DB::table('transactions')
            ->leftJoin('assets', function($join) {
                $join->on('transactions.item_id', '=', 'assets.id')
                     ->where('transactions.item_type', '=', 'assets');
            })
            ->leftJoin('supplies', function($join) {
                $join->on('transactions.item_id', '=', 'supplies.id')
                     ->where('transactions.item_type', '=', 'supplies');
            })
            ->select(
                'transactions.*',
                DB::raw('COALESCE(assets.article, supplies.article) as item_name'),
                DB::raw('COALESCE(assets.barcode_id, supplies.barcode_id) as item_code')
            )
            ->where('transactions.id', $id)
            ->first();
    }

    /**
     * Get transaction history for a specific item
     */
    public function getItemTransactionHistory(int $itemId, string $itemType, int $limit = 50)
    {
        return DB::table('transactions')
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->orderBy('date_time', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get RIS items from transaction history
     */
    public function getRisItems(int $perPage = 20, string $search = '')
    {
        $query = DB::table('transactions as t')
            ->leftJoin('supplies as s', function($join) {
                $join->on('t.item_id', '=', 's.id')
                     ->where('t.item_type', '=', 'supplies');
            })
            ->leftJoin('ris_requests as r', function($join) {
                $join->whereRaw("t.remarks LIKE CONCAT('%', r.ris_no, '%')");
            })
            ->where('t.transaction_type', 'OUT')
            ->where('t.remarks', 'LIKE', 'RIS%')
            ->where('t.quantity', '>', 0);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('s.article', 'LIKE', "%{$search}%")
                  ->orWhere('t.remarks', 'LIKE', "%{$search}%");
            });
        }

        return $query->select(
            't.id',
            't.item_id',
            't.remarks',
            't.quantity',
            't.date_time',
            's.article as item_name',
            's.barcode_id as item_code',
            'r.ris_no',
            'r.sig_requested_by'
        )
        ->orderBy('t.date_time', 'desc')
        ->paginate($perPage);
    }

    /**
     * Get summary statistics for dashboard
     */
    public function getTransactionSummary(array $filters = []): array
    {
        $query = DB::table('transactions')
            ->where('quantity', '>', 0);

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_time', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date_time', '<=', $filters['date_to']);
        }

        return [
            'total_transactions' => $query->count(),
            'total_in' => DB::table('transactions')
                ->where('transaction_type', 'IN')
                ->where('quantity', '>', 0)
                ->sum('quantity'),
            'total_out' => DB::table('transactions')
                ->where('transaction_type', 'OUT')
                ->where('quantity', '>', 0)
                ->sum('quantity'),
            'total_by_type' => DB::table('transactions')
                ->select('transaction_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_qty'))
                ->where('quantity', '>', 0)
                ->groupBy('transaction_type')
                ->get()
        ];
    }
}
