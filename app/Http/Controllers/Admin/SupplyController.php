<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use App\Models\SupplySection;
use App\Models\Transaction;
use App\Models\PurchaseOrderItem;
use App\Models\ActivityLog;
use App\Services\SupplyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SupplyController extends Controller
{
    public function index(Request $request)
    {
        $query = Supply::select('supplies.*')
            ->selectRaw('(SELECT COALESCE(SUM(quantity), 0) FROM transactions WHERE transactions.item_id = supplies.id AND transactions.item_type = "supplies" AND transactions.transaction_type IN ("IN", "Added")) as total_input');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                                $q->where('article', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand_filter') && $request->brand_filter !== 'All') {
            $query->where('brand', $request->brand_filter);
        }

        if ($request->filled('status_filter') && $request->status_filter !== 'All') {
            if ($request->status_filter === 'Available') {
                $query->whereColumn('quantity', '>', 'low_stock_threshold');
            } elseif ($request->status_filter === 'Low Stock') {
                $query->whereColumn('quantity', '<=', 'low_stock_threshold')
                      ->where('quantity', '>', 0);
            } elseif ($request->status_filter === 'Out of Stock') {
                $query->where('quantity', '<=', 0);
            }
        }

        $supplies = $query->orderBy('article')->orderByRaw('classification IS NULL, classification')->orderBy('id', 'desc')->get();

        // Two-level accordion grouping: Section (article) -> Classification -> items
        $suppliesGrouped = $supplies->groupBy(fn ($s) => $s->article ?: 'Uncategorized')
            ->map(fn ($bySection) => $bySection->groupBy(fn ($s) => $s->classification ?: 'General'));

        // Merge quick-added sections that have no supplies yet, so they still
        // appear in the list (as empty groups) instead of vanishing.
        foreach (SupplySection::sectionMap() as $sectionName => $classifications) {
            $sectionGroup = $suppliesGrouped->get($sectionName) ?? collect();

            foreach ($classifications as $classification) {
                if (! $sectionGroup->has($classification)) {
                    $sectionGroup->put($classification, collect());
                }
            }

            $suppliesGrouped->put($sectionName, $sectionGroup->sortKeys());
        }
        $suppliesGrouped = $suppliesGrouped->sortKeys();

        $totalSupplyCount = $supplies->count();

        $brandOptions = Supply::whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand')->pluck('brand');

        // Sections (article) mapped to their existing classifications, for the multi-level supply dropdown
        $sections = Supply::whereNotNull('article')->where('article', '!=', '')
            ->orderBy('article')
            ->get(['article', 'classification'])
            ->groupBy('article')
            ->map(function ($group) {
                return $group->pluck('classification')->filter()->unique()->sort()->values();
            });

        // Merge quick-added sections so they're always offered in the dropdowns
        $sections = SupplySection::mergeWithExisting($sections);

        $deliveredPoItems = collect();
        if (class_exists(PurchaseOrderItem::class)) {
            $existingSupplyDescriptions = Supply::pluck('description')->map(function($desc) {
                return strtolower(trim($desc));
            })->toArray();

            $rawPoItems = PurchaseOrderItem::with('purchaseOrder')
                ->whereHas('purchaseOrder', function($q) {
                    $q->where('po_type', 'Supply'); 
                })
                ->where('item_type', 'supply')
                ->where('source_type', '!=', 'direct_issuance')
                ->where('is_delivered', true)
                ->get();

            $deliveredPoItems = $rawPoItems->reject(function($item) use ($existingSupplyDescriptions) {
                return in_array(strtolower(trim($item->description)), $existingSupplyDescriptions);
            });
        }
        
        return view('admin.supplies.index', compact('suppliesGrouped', 'totalSupplyCount', 'deliveredPoItems', 'brandOptions', 'sections'));
    }

    /**
     * Quick-add a Supply Section + Classification pair (AJAX from the
     * supplies page header button). Idempotent on the name+classification pair.
     */
    public function quickStoreSection(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'classification' => 'required|string|max:255',
        ], [
            'name.required'           => 'Section name is required.',
            'classification.required' => 'Classification is required.',
        ]);

        $section = SupplySection::firstOrCreate([
            'name'           => trim($data['name']),
            'classification' => trim($data['classification']),
        ]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'Created',
            'description' => "Quick-added supply section: {$section->name} › {$section->classification}",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return response()->json([
            'status'  => $section->wasRecentlyCreated ? 'created' : 'exists',
            'name'    => $section->name,
            'classification' => $section->classification,
        ]);
    }

    /**
     * All quick-added sections for the manage UI, grouped by name with the
     * id of the first row (the id used by rename/delete endpoints).
     */
    public function manageSections()
    {
        $sections = SupplySection::query()
            ->orderBy('name')
            ->orderBy('classification')
            ->get(['id', 'name', 'classification'])
            ->groupBy('name')
            ->map(fn ($rows) => [
                'id'             => $rows->first()->id,
                'name'           => $rows->first()->name,
                'classifications' => $rows->pluck('classification')->filter()->unique()->values(),
            ])
            ->values();

        return response()->json($sections);
    }

    /**
     * Rename a quick-added supply section (all its classifications move with it).
     */
    public function updateSection(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Section name is required.',
        ]);
        
        $section = SupplySection::findOrFail($id);
        $oldName = $section->name;
        $newName = trim($data['name']);

        if ($newName !== $oldName) {
            SupplySection::where('name', $oldName)->update(['name' => $newName]);

            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'Updated',
                'description' => "Renamed supply section: {$oldName} → {$newName}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        return response()->json(['status' => 'success', 'name' => $newName]);
    }

    /**
     * Delete one quick-added classification, or the entire section when no
     * classification is given. Supplies already filed under the section are
     * left untouched.
     */
    public function destroySection(Request $request, $id)
    {
        $request->validate([
            'classification' => 'nullable|string',
        ]);

        $section = SupplySection::findOrFail($id);

        if ($request->filled('classification')) {
            $deleted = SupplySection::where('name', $section->name)
                ->where('classification', $request->classification)
                ->delete();

            if ($deleted === 0)  {
                return response()->json(['status' => 'error', 'message' => 'Classification not found.'], 404);
            }

            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'Deleted',
                'description' => "Removed supply classification: {$section->name} › {$request->classification}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        } else {
            $deleted = SupplySection::where('name', $section->name)->delete();

            if ($deleted === 0) {
                return response()->json(['status' => 'error', 'message' => 'Section not found.'], 404);
            }

            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'Deleted',
                'description' => "Removed supply section: {$section->name}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }
        
        return response()->json(['status' => 'success']);
    }

    public function store(Request $request)
    {
        $status = ($request->initial_quantity > 0) ? 'Available' : 'Out of Stock';

        if (!$request->has('force_save')) {
            $existing = Supply::where('article', trim($request->article))
                ->where('description', trim($request->description))
                ->where('unit_measure', trim($request->unit_measure))
                ->where('unit_value', $request->unit_value);
                
            if ($request->filled('supplier')) {
                $existing->where('supplier', trim($request->supplier));
            } else {
                $existing->where(function($q) {
                    $q->whereNull('supplier')->orWhere('supplier', '');
                });
            }

            if ($request->filled('brand')) {
                $existing->where('brand', trim($request->brand));
            } else {
                $existing->where(function($q) {
                    $q->whereNull('brand')->orWhere('brand', '');
                });
            }

            if ($request->filled('model')) {
                $existing->where('model', trim($request->model));
            } else {
                $existing->where(function($q) {
                    $q->whereNull('model')->orWhere('model', '');
                });
            }

            if ($request->filled('classification')) {
                $existing->where('classification', trim($request->classification));
            } else {
                $existing->where(function($q) {
                    $q->whereNull('classification')->orWhere('classification', '');
                });
            }

            $duplicate = $existing->first();

            if ($duplicate) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'duplicate',
                        'existing_id' => $duplicate->id
                    ]);
                }
            }
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('supplies', $imageName, 'public');
        }

        $supply = Supply::create([
            'article' => $request->article,
            'description' => $request->description,
            'brand' => $request->brand,
            'model' => $request->model,
            'classification' => $request->classification,
            'unit_measure' => $request->unit_measure,
            'unit_value' => $request->unit_value,
            'quantity' => $request->initial_quantity,
            'low_stock_threshold' => $request->low_stock_threshold ?? 10,
            'supplier' => $request->supplier,
            'status' => $status,
            'image' => $imageName
        ]);

        // Keep the quick-add registry in sync: a section/classification pair
        // first used through the Add Supply form is filed into supply_sections
        // so it shows up under Manage Sections and in every dropdown, exactly
        // as if it had been created with the dedicated quick-add button.
        $sectionName = trim((string) $supply->article);
        $classificationName = trim((string) $supply->classification);

        if ($sectionName !== '' && $classificationName !== '') {
            SupplySection::firstOrCreate([
                'name'           => $sectionName,
                'classification' => $classificationName,
            ]);
        }

        Transaction::create([
            'item_id' => $supply->id,
            'item_type' => 'supplies',
            'transaction_type' => 'Added',
            'quantity' => $request->initial_quantity ?? 0,
            'supplier' => $request->supplier,
            'transaction_date' => date('Y-m-d'),
            'remarks' => 'Opening Balance / New Item',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created',
            'description' => "Added new supply item: {$supply->article}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return redirect('/admin/supplies')->with('msg', 'Supply successfully added!');
    }

    public function update(Request $request, $id)
    {
        $supply = Supply::findOrFail($id);
        
        $imageName = $supply->image;
        if ($request->hasFile('image')) {
            $oldImageName = $supply->image;
            $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $request->image->extension();
            $request->image->storeAs('supplies', $imageName, 'public');
        }

        $supply->update([
            'article' => $request->article,
            'description' => $request->description,
            'brand' => $request->brand,
            'model' => $request->model,
            'classification' => $request->classification,
            'unit_measure' => $request->unit_measure,
            'unit_value' => $request->unit_value,
            'quantity' => $request->quantity,
            'low_stock_threshold' => $request->low_stock_threshold ?? 10,
            'supplier' => $request->supplier,
            'status' => $request->status ?? 'Available',
            'image' => $imageName
        ]);

        if (isset($oldImageName) && $oldImageName && Storage::disk('public')->exists('supplies/' . $oldImageName)) {
            Storage::disk('public')->delete('supplies/' . $oldImageName);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated',
            'description' => "Updated supply details: {$supply->article}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/admin/supplies')->with('msg', 'Supply successfully updated!');
    }

    public function destroy($id)
    {
        $supply = Supply::findOrFail($id);
        
        if ($supply->image && Storage::disk('public')->exists('supplies/' . $supply->image)) {
            Storage::disk('public')->delete('supplies/' . $supply->image);
        }
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted',
            'description' => "Deleted supply item: {$supply->article}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $supply->delete();
        Transaction::where('item_id', $id)->where('item_type', 'supplies')->delete();

        return redirect('/admin/supplies')->with('msg', 'Supply successfully deleted!');
    }

    public function details($id)
    {
        $supply = Supply::find($id);

        if (!$supply) return '<div class="p-4 text-center text-danger">Supply details not found.</div>';

        $currentQty = max(0, intval($supply->quantity));
        $unitValue = floatval($supply->unit_value);
        $threshold = intval($supply->low_stock_threshold ?? 10);
        $unit = $supply->unit_measure ?: 'unit(s)';

        $formattedUnitValue = number_format($unitValue, 2);

        $supplierName = !empty($supply->supplier) ? htmlspecialchars($supply->supplier) : 'N/A';
        $brandName = !empty($supply->brand) ? htmlspecialchars($supply->brand) : 'N/A';
        $modelName = !empty($supply->model) ? htmlspecialchars($supply->model) : 'N/A';
        $articleName = htmlspecialchars($supply->article);
        $descriptionName = htmlspecialchars($supply->description);

        $lowStock = $currentQty > 0 && $currentQty <= $threshold;
        $outOfStock = $currentQty <= 0;

        if ($outOfStock) {
            $statusLabel = 'Out of Stock';
            $statusClass = 'ov-status-danger';
            $statusIcon = 'fa-box-open';
        } elseif ($lowStock) {
            $statusLabel = 'Low Stock';
            $statusClass = 'ov-status-warning';
            $statusIcon = 'fa-triangle-exclamation';
        } else {
            $statusLabel = 'Available';
            $statusClass = 'ov-status-success';
            $statusIcon = 'fa-circle-check';
        }

        $imageHtml = '<div class="ov-avatar ov-avatar-empty"><i class="fas fa-boxes-stacked"></i></div>';
        $lightboxHtml = '';

        if (!empty($supply->image) && file_exists(storage_path('app/public/supplies/' . $supply->image))) {
            $imageUrl = asset('storage/supplies/' . $supply->image);
            $imageHtml = '<div class="ov-avatar ov-avatar-zoom" title="Click to enlarge image" onclick="document.getElementById(\'ov-lightbox-' . $id . '\').style.display=\'flex\'"><img src="' . $imageUrl . '" alt="Supply Image"></div>';

            $lightboxHtml = '
            <div id="ov-lightbox-' . $id . '" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.88); align-items:center; justify-content:center; flex-direction:column; backdrop-filter: blur(6px);" onclick="this.style.display=\'none\'">
                <span style="position:absolute; top:20px; right:30px; color:white; font-size:40px; cursor:pointer; font-weight:bold;">&times;</span>
                <img src="' . $imageUrl . '" style="max-width:90%; max-height:85vh; border-radius:14px; box-shadow:0 25px 60px rgba(0,0,0,0.5);">
                <div class="text-white mt-3 fw-bold fs-5">' . $articleName . '</div>
            </div>';
        }

        $chips = [];
        if (!empty($supply->brand))    $chips[] = '<span class="ov-chip"><i class="fas fa-copyright"></i>' . $brandName . '</span>';
        if (!empty($supply->model))    $chips[] = '<span class="ov-chip"><i class="fas fa-microchip"></i>' . $modelName . '</span>';
        if (!empty($supply->supplier)) $chips[] = '<span class="ov-chip"><i class="fas fa-truck-fast"></i>' . $supplierName . '</span>';
        $chipsHtml = $chips ? '<div class="ov-chips">' . implode('', $chips) . '</div>' : '';

        $overviewItems = [
            ['fa-layer-group',          'Section',             $articleName !== '' ? $articleName : '—', ''],
            ['fa-tags',                 'Classification',      !empty($supply->classification) ? htmlspecialchars($supply->classification) : 'General', ''],
            ['fa-ruler-combined',       'Unit of Measure',     htmlspecialchars($unit), ''],
            ['fa-tag',                  'Unit Value',          '₱' . $formattedUnitValue, 'per ' . htmlspecialchars($unit)],
            ['fa-triangle-exclamation', 'Low Stock Threshold', $threshold, 'alert when at or below'],
        ];

        $overviewRows = '';
        foreach ($overviewItems as [$icon, $label, $value, $hint]) {
            $hintHtml = $hint !== '' ? '<div class="ov-item-hint">' . $hint . '</div>' : '';
            $overviewRows .= '
                <div class="ov-item">
                    <div class="ov-item-icon"><i class="fas ' . $icon . '"></i></div>
                    <div>
                        <div class="ov-item-label">' . $label . '</div>
                        <div class="ov-item-value">' . $value . '</div>' . $hintHtml . '
                    </div>
                </div>';
        }

        return <<<HTML
        {$lightboxHtml}
        <style>
            .ov-root { border-radius: 10px; overflow: hidden; background: #f4f6fb; font-size: .95rem; }
            .ov-hero { position: relative; padding: 1.6rem 1.6rem 1.4rem; background: linear-gradient(135deg, #0b1c3f 0%, #16307c 55%, #2b4bb3 100%); color: #fff; }
            .ov-hero::after { content: ""; position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.07); }
            .ov-hero-main { position: relative; z-index: 1; display: flex; gap: 1rem; align-items: flex-start; }
            .ov-hero-main > div:last-child { min-width: 0; }
            .ov-kicker { font-size: .7rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; opacity: .65; margin-bottom: .15rem; }
            .ov-title { font-size: 1.3rem; font-weight: 800; line-height: 1.2; margin: 0; }
            .ov-sub { font-size: .85rem; opacity: .75; margin-top: .15rem; word-break: break-word; }
            .ov-avatar { width: 76px; height: 76px; flex-shrink: 0; border-radius: 18px; overflow: hidden; background: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; }
            .ov-avatar img { width: 100%; height: 100%; object-fit: cover; }
            .ov-avatar-zoom { cursor: pointer; transition: transform .2s; }
            .ov-avatar-zoom:hover { transform: scale(1.05); }
            .ov-avatar-empty { font-size: 1.9rem; color: #2b4bb3; }
            .ov-chips { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: .4rem; margin-top: 1.1rem; }
            .ov-chip { display: inline-flex; align-items: center; gap: .4rem; background: rgba(255,255,255,0.13); border: 1px solid rgba(255,255,255,0.18); color: #fff; border-radius: 999px; padding: .28rem .7rem; font-size: .75rem; font-weight: 600; }
            .ov-chip i { font-size: .7rem; opacity: .8; }
            .ov-body { padding: 1.25rem 1.6rem .5rem; }
            .ov-stock { display: flex; align-items: center; justify-content: space-between; gap: 1rem; background: #fff; border: 1px solid #e8ecf4; border-radius: 14px; padding: 1rem 1.3rem; box-shadow: 0 2px 10px rgba(15,23,42,0.05); }
            .ov-stock-number { font-size: 2.4rem; font-weight: 800; line-height: 1; color: #0f172a; }
            .ov-stock-unit { font-size: .85rem; color: #64748b; font-weight: 600; margin-left: .35rem; }
            .ov-stock-caption { font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #94a3b8; margin-top: .3rem; }
            .ov-status { display: inline-flex; align-items: center; gap: .45rem; border-radius: 999px; padding: .4rem .85rem; font-size: .8rem; font-weight: 700; white-space: nowrap; }
            .ov-status-success { background: #dcfce7; color: #15803d; }
            .ov-status-warning { background: #fef3c7; color: #b45309; }
            .ov-status-danger  { background: #ffe4e6; color: #be123c; }
            .ov-status i { font-size: .8rem; }
            .ov-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-top: .9rem; }
            @media (max-width: 576px) { .ov-grid { grid-template-columns: 1fr; } .ov-stock { flex-direction: column; align-items: flex-start; } }
            .ov-item { display: flex; align-items: flex-start; gap: .75rem; background: #fff; border: 1px solid #e8ecf4; border-radius: 12px; padding: .8rem .9rem; }
            .ov-item-icon { width: 34px; height: 34px; flex-shrink: 0; border-radius: 10px; background: #eef2ff; color: #2b4bb3; display: flex; align-items: center; justify-content: center; font-size: .85rem; }
            .ov-item-label { font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: #94a3b8; }
            .ov-item-value { font-size: .92rem; font-weight: 700; color: #0f172a; word-break: break-word; }
            .ov-item-hint { font-size: .72rem; color: #94a3b8; margin-top: .1rem; }
            .ov-footer { display: flex; justify-content: center; padding: 1.1rem 1.6rem 1.4rem; }
            .ov-footer .btn { background: #0b1c3f; border: none; color: #fff; font-weight: 700; border-radius: 999px; padding: .6rem 2.4rem; }
            .ov-footer .btn:hover { background: #16307c; }
        </style>
        <div class="ov-root">
            <div class="ov-hero">
                <div class="ov-hero-main">
                    {$imageHtml}
                    <div class="flex-grow-1">
                        <div class="ov-kicker">Supply Overview</div>
                        <h5 class="ov-title">{$articleName}</h5>
                        <div class="ov-sub">{$descriptionName}</div>
                    </div>
                </div>
                {$chipsHtml}
            </div>
            <div class="ov-body">
                <div class="ov-stock">
                    <div>
                        <div class="ov-stock-caption">Current Stock</div>
                        <div><span class="ov-stock-number">{$currentQty}</span><span class="ov-stock-unit">{$unit}</span></div>
                    </div>
                    <span class="ov-status {$statusClass}"><i class="fas {$statusIcon}"></i>{$statusLabel}</span>
                </div>
                <div class="ov-grid">
                    {$overviewRows}
                </div>
            </div>
            <div class="ov-footer"><button type="button" class="btn" data-bs-dismiss="modal">Close Window</button></div>
        </div>
HTML;
    }

    public function stockTransaction(Request $request, SupplyService $supplyService, $id)
    {
        $supply = Supply::findOrFail($id);

        try {
            $supplyService->processStockTransaction($supply, [
                'type' => $request->transaction_type,
                'quantity' => $request->qty,
                'supplier' => $request->supplier,
                'unit_price' => $request->unit_price,
                'transaction_date' => $request->transaction_date,
                'remarks' => $request->remarks,
            ]);
        } catch (\InvalidArgumentException|\DomainException $e) {
            $message = str_starts_with($e->getMessage(), 'Insufficient stock')
                ? 'error_stock'
                : 'error_transaction';

            return redirect('/admin/supplies')->with('msg', $message);
        }

        return redirect('/admin/supplies')->with('msg', 'Supply stock updated successfully!');
    }
}