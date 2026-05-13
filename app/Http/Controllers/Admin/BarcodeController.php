<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);
        
        // Grab search and category parameters from the URL
        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $supplies = collect();
        $assets = collect();

        // 1. Fetch from Supplies 
        if ($category === 'all' || $category === 'supply') {
            $supplyQuery = Supply::whereNotNull('barcode_id')
                ->select('id', 'barcode_id as barcode_code', 'article', 'description', 'supplier');
                
            // Apply Search Filter to Database
            if (!empty($search)) {
                $supplyQuery->where(function($q) use ($search) {
                    $q->where('article', 'LIKE', "%{$search}%")
                      ->orWhere('barcode_id', 'LIKE', "%{$search}%");
                });
            }

            $supplies = $supplyQuery->get()->map(function ($item) {
                $item->item_type = 'supply';
                $item->generated_at = null; 
                return $item;
            });
        }

        // 2. Fetch from Assets 
        if ($category === 'all' || $category === 'asset') {
            $assetQuery = Asset::whereNotNull('barcode_id')
                ->select('id', 'barcode_id as barcode_code', 'article', 'description', 'supplier');
                
            // Apply Search Filter to Database
            if (!empty($search)) {
                $assetQuery->where(function($q) use ($search) {
                    $q->where('article', 'LIKE', "%{$search}%")
                      ->orWhere('barcode_id', 'LIKE', "%{$search}%");
                });
            }

            $assets = $assetQuery->get()->map(function ($item) {
                $item->item_type = 'asset';
                $item->generated_at = null; 
                return $item;
            });
        }

        // 3. Merge both lists and sort by ID descending (highest ID = newest added)
        $mergedBarcodes = $supplies->concat($assets)->sortByDesc('id')->values();

        // 4. Manually Paginate the merged collection
        $offset = ($page * $perPage) - $perPage;
        $itemsForCurrentPage = $mergedBarcodes->slice($offset, $perPage)->all();
        
        $barcodes = new LengthAwarePaginator(
            $itemsForCurrentPage, 
            $mergedBarcodes->count(), 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.barcodes.index', compact('barcodes', 'perPage', 'search', 'category'));
    }
}