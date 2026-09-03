<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PurchaseOrderController;
use App\Models\PurchaseOrder;
use App\Models\Supply;
use Illuminate\Http\Request;

/**
 * Admin Purchase Orders — delegates store/update/show/destroy to the staff
 * PurchaseOrderController so both sides share the same features (auto-RIS for
 * direct issuance, supply syncing, item types, fulfillment modes, etc.).
 * Index is overridden to render the admin-specific view.
 */
class PoController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_no', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_filter') && $request->status_filter !== 'All') {
            $query->where('status', $request->status_filter);
        }

        $sort = $request->input('sort', 'date_desc');
        if ($sort === 'supplier_asc') {
            $query->orderBy('supplier_name', 'asc');
        } elseif ($sort === 'supplier_desc') {
            $query->orderBy('supplier_name', 'desc');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('po_date', 'asc');
        } else {
            $query->orderBy('po_date', 'desc');
        }

        $purchaseOrders = $query->get();
        $supplies = Supply::orderBy('article')->get(['id', 'article', 'description', 'unit_measure']);

        return view('admin.po.index', compact('purchaseOrders', 'supplies'));
    }

    public function store(Request $request)
    {
        return app(PurchaseOrderController::class)->store($request);
    }

    public function show($id)
    {
        return app(PurchaseOrderController::class)->show($id);
    }

    public function update(Request $request, $id)
    {
        return app(PurchaseOrderController::class)->update($request, $id);
    }

    public function destroy($id)
    {
        return app(PurchaseOrderController::class)->destroy($id);
    }
}