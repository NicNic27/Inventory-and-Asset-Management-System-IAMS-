<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);
        
        // Grab search and category parameters from the URL
        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $assets = collect();
        // Fetch asset barcodes only.
        if ($category === 'all' || $category === 'asset') {
            $assetFields = ['id', 'barcode_id as barcode_code'];
            $assetHasArticle = Schema::hasColumn('assets', 'article');
            $assetHasDescription = Schema::hasColumn('assets', 'description');
            $assetHasSupplier = Schema::hasColumn('assets', 'supplier');

            if ($assetHasArticle) {
                $assetFields[] = 'article';
            }
            if ($assetHasDescription) {
                $assetFields[] = 'description';
            }
            if ($assetHasSupplier) {
                $assetFields[] = 'supplier';
            }

            $assetQuery = Asset::whereNotNull('barcode_id')->select($assetFields);

            // Apply Search Filter to Database
            if (!empty($search)) {
                $assetQuery->where(function($q) use ($search, $assetHasArticle) {
                    if ($assetHasArticle) {
                        $q->where('article', 'LIKE', "%{$search}%")
                          ->orWhere('barcode_id', 'LIKE', "%{$search}%");
                    } else {
                        $q->where('barcode_id', 'LIKE', "%{$search}%");
                    }
                });
            }

            $assets = $assetQuery->get()->map(function ($item) {
                $item->item_type = 'asset';
                $item->generated_at = null; 
                return $item;
            });
        }

        // Sort by ID descending (highest ID = newest added).
        $mergedBarcodes = $assets->sortByDesc('id')->values();

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