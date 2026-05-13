<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Inventory - DepEd AMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            overflow: hidden; 
            height: 100vh;
            margin: 0;
        }

        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
            padding-top: 80px !important; 
            transition: all 0.3s; 
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .table-container { 
            background: white; 
            padding: 20px 20px 10px 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
            flex-grow: 1; 
            display: flex;
            flex-direction: column;
            min-height: 0; 
        }

        .table-responsive {
            flex-grow: 1;
            overflow-y: auto; 
            margin-bottom: 10px;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .status-serviceable { background-color: #198754; color: #fff; } 
        .status-assigned { background-color: #101954; color: #fff; } 
        .status-defective { background-color: #dc3545; color: #fff; } 
        
        .clickable-row { cursor: pointer; transition: background-color 0.2s; }
        .clickable-row:hover { background-color: #f8f9fa !important; }

        #scrollablePagination nav > div:not(:last-child),
        #scrollablePagination p { display: none !important; }

        .custom-pagination-wrapper ul.pagination {
            position: relative; 
            display: flex; 
            flex-wrap: nowrap;
            max-width: 250px; 
            overflow-x: auto; 
            overflow-y: hidden;
            scrollbar-width: thin; 
            scrollbar-color: #101954 #f4f6f9;
            padding-bottom: 4px;
            margin-bottom: 0;
        }
        
        .custom-pagination-wrapper ul.pagination::-webkit-scrollbar { height: 6px; }
        .custom-pagination-wrapper ul.pagination::-webkit-scrollbar-track { background: #f4f6f9; border-radius: 10px; }
        .custom-pagination-wrapper ul.pagination::-webkit-scrollbar-thumb { background: #101954; border-radius: 10px; }

        .custom-pagination-wrapper ul.pagination > li:first-child { position: sticky; left: 0; z-index: 5; }
        .custom-pagination-wrapper ul.pagination > li:last-child { position: sticky; right: 0; z-index: 5; }
        
        .custom-pagination-wrapper ul.pagination > li:first-child .page-link,
        .custom-pagination-wrapper ul.pagination > li:last-child .page-link {
            background-color: white !important;
            box-shadow: 0 0 5px rgba(0,0,0,0.15);
        }

        .page-item.active .page-link { background-color: #f4f6f9; color: #101954; font-weight: 700; border-color: #dee2e6; }
        .page-link { color: #6c757d; }
        .page-link:hover { color: #101954; background-color: #f4f6f9; }

        .modal { z-index: 1060 !important; }
        .modal-backdrop { z-index: 1055 !important; }
        
        @media (max-width: 768px) { 
            .main-content { margin-left: 0; height: auto; overflow: visible; } 
            body { overflow: visible; height: auto; }
            .table-container { min-height: 500px; }
        }
    </style>
</head>
<body>

    @include('layouts.admin_header')
    @include('layouts.admin_sidebar')

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold text-dark mb-0" style="color: #003366 !important;">
                    <i class="fas fa-laptop text-primary me-2"></i>Assets Inventory (PPE)
                </h3>
            </div>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                <i class="fas fa-plus me-2"></i> Add New Asset
            </button>
        </div>

        @if(session('msg') == 'saved')
            <div class="alert alert-success alert-dismissible fade show py-2 border-0 shadow-sm">
                <i class="fas fa-check-circle me-2"></i> Asset successfully saved/updated! 
                <button type="button" class="btn-close btn-sm pt-3" data-bs-dismiss="alert"></button>
            </div>
        @elseif(session('msg') == 'deleted')
            <div class="alert alert-success alert-dismissible fade show py-2 border-0 shadow-sm">
                <i class="fas fa-trash-alt me-2"></i> Asset successfully deleted! 
                <button type="button" class="btn-close btn-sm pt-3" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-container">
            
            <form action="{{ url('/admin/assets') }}" method="GET" id="filterForm" class="d-flex justify-content-between align-items-center mb-3 pe-2 gap-2">
                <div class="d-flex gap-2 flex-grow-1">
                    <div class="input-group shadow-sm" style="max-width: 350px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" id="assetSearchInput" class="form-control border-start-0 ps-0" placeholder="Search Property No., Article, or Desc..." value="{{ request('search') }}">
                    </div>
                    
                    <select name="status_filter" class="form-select shadow-sm" style="max-width: 180px;" onchange="document.getElementById('filterForm').submit();">
                        <option value="All" {{ request('status_filter') == 'All' ? 'selected' : '' }}>All Statuses</option>
                        <option value="Serviceable" {{ request('status_filter') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                        <option value="Unserviceable" {{ request('status_filter') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable / Defective</option>
                    </select>
                </div>

                @if(request('search') || request('status_filter') && request('status_filter') !== 'All')
                    <a href="{{ url('/admin/assets') }}" class="btn btn-outline-danger btn-sm fw-bold shadow-sm"><i class="fas fa-times me-1"></i> Clear Filters</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">Property No.</th>
                            <th class="text-nowrap">Article / Item</th>
                            <th class="text-nowrap" style="min-width: 200px;">Description</th>
                            <th>Unit</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $row)
                            @php
                                $stockNo = !empty($row->barcode_id) ? $row->barcode_id : '<span class="text-muted small">No Property No.</span>';
                                
                                if ($row->status != 'Serviceable') {
                                    $statusHtml = '<span class="badge rounded-pill status-defective px-3 py-1">'.$row->status.'</span>';
                                } elseif ($row->assigned_to) {
                                    $statusHtml = '<span class="badge rounded-pill status-assigned shadow-sm px-3 py-1" title="Issued to: '.$row->assigned_to.'"><i class="fas fa-user-check me-1"></i> Assigned</span>';
                                } else {
                                    $statusHtml = '<span class="badge rounded-pill status-serviceable shadow-sm px-3 py-1"><i class="fas fa-check-circle me-1"></i> Available</span>';
                                }
                            @endphp
                            <tr class="clickable-row" data-id="{{ $row->id }}">
                                <td class="fw-bold text-primary font-monospace">{!! $stockNo !!}</td>
                                <td class="fw-bold text-nowrap">{{ $row->article }}</td>
                                <td><small class="text-muted">{{ Str::limit($row->description, 40) }}</small></td>
                                <td>{{ $row->unit_measure }}</td>
                                <td>₱{{ number_format($row->unit_value, 2) }}</td>
                                <td>{!! $statusHtml !!}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-light border text-primary view-btn" 
                                                title="View" 
                                                data-id="{{ $row->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button class="btn btn-sm btn-light border text-success edit-btn" 
                                                title="Edit"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editAssetModal"
                                                data-id="{{ $row->id }}"
                                                data-article="{{ $row->article }}"
                                                data-stock="{{ $row->barcode_id }}"
                                                data-desc="{{ $row->description }}"
                                                data-unit="{{ $row->unit_measure }}"
                                                data-value="{{ $row->unit_value }}"
                                                data-status="{{ $row->status }}"
                                                data-image="{{ $row->image }}"
                                                data-supplier="{{ $row->supplier }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-light border text-danger delete-btn" 
                                                title="Delete"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteAssetModal"
                                                data-id="{{ $row->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted border-bottom-0">
                                    <i class="fas fa-laptop fa-3x mb-3 opacity-25 d-block"></i>
                                    No assets match your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                <div class="text-muted small">
                    Showing {{ $assets->firstItem() ?? 0 }} to {{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }} results
                </div>

                <div class="d-flex align-items-center">
                    <span class="text-muted small me-2">Per page</span>
                    <form action="{{ url('/admin/assets') }}" method="GET" id="perPageForm">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('status_filter')) <input type="hidden" name="status_filter" value="{{ request('status_filter') }}"> @endif
                        <select name="per_page" class="form-select form-select-sm shadow-none" style="width: 70px; border-color: #101954; color: #101954; font-weight: 500;" onchange="document.getElementById('perPageForm').submit();">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                </div>

                <div class="custom-pagination-wrapper" id="scrollablePagination">
                    {{ $assets->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="addAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add New Asset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addAssetForm" action="{{ url('/admin/assets') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-3 text-center border-end pe-4">
                                <label class="form-label fw-bold d-block text-start">Asset Image</label>
                                <div class="border rounded bg-light d-flex justify-content-center align-items-center mx-auto mb-3 overflow-hidden shadow-sm" 
                                     style="width: 100%; aspect-ratio: 1/1; position: relative;">
                                    <img id="imagePreviewAdd" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                    <i id="imagePlaceholderAdd" class="fas fa-image fa-4x text-muted opacity-50"></i>
                                </div>
                                <input type="file" name="image" id="imageInputAdd" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted text-start d-block mt-2">Recommended: Square format (JPG, PNG)</small>
                            </div>
                            
                            <div class="col-md-9 ps-4">
                                @if(isset($deliveredPoItems) && count($deliveredPoItems) > 0)
                                    <div class="mb-4 bg-light p-3 rounded border">
                                        <label class="form-label text-primary fw-bold mb-2"><i class="fas fa-magic me-1"></i> Auto-Fill from Delivered P.O. (Optional)</label>
                                        <select id="po_autofill_select" class="form-select border-primary shadow-sm" onchange="autoFillAssetForm(this)">
                                            <option value="">Select a delivered item to auto-fill the form...</option>
                                                @php
                                                    $groupedItems = $deliveredPoItems->groupBy(function($item) {
                                                        return $item->purchaseOrder->po_no ?? 'Unknown PO';
                                                    });
                                                @endphp
                                                @foreach($groupedItems as $poNo => $items)
                                                    <optgroup label="P.O. {{ $poNo }}">
                                                        @foreach($items as $item)
                                                            <option value="{{ $item->id }}" 
                                                                    data-desc="{{ $item->description }}"
                                                                    data-supplier="{{ $item->purchaseOrder->supplier_name ?? '' }}"
                                                                    data-unit="{{ $item->unit }}"
                                                                    data-val="{{ $item->unit_cost }}">
                                                                {{ Str::limit($item->description, 45) }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Article (Name) <span class="text-danger">*</span></label>
                                        <input type="text" name="article" id="add_article" class="form-control" required placeholder="e.g. Dell Laptop">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Supplier</label>
                                        <input type="text" name="supplier" id="add_supplier" class="form-control" placeholder="e.g. PC Express">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea name="description" id="add_desc" class="form-control" rows="2" placeholder="e.g. Core i5, 8GB RAM, 256GB SSD"></textarea>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold text-primary">Property No. (Barcode ID) <span class="text-danger">*</span></label>
                                        <input type="text" name="barcode_id" class="form-control border-primary border-2" required placeholder="Enter Property Number manually...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Measure <span class="text-danger">*</span></label>
                                        <input type="text" name="unit_measure" id="add_unit" class="form-control" placeholder="e.g. Unit, Set" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Value (₱) <span class="text-danger">*</span></label>
                                        <input type="number" name="unit_value" id="add_val" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                </div>
                                <input type="hidden" name="status" value="Serviceable">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Save Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Asset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-3 text-center border-end pe-4">
                                <label class="form-label fw-bold d-block text-start">Update Image (Optional)</label>
                                <div class="border rounded bg-light d-flex justify-content-center align-items-center mx-auto mb-3 overflow-hidden shadow-sm" 
                                     style="width: 100%; aspect-ratio: 1/1; position: relative;">
                                    <img id="imagePreviewEdit" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                    <i id="imagePlaceholderEdit" class="fas fa-image fa-4x text-muted opacity-50"></i>
                                </div>
                                <input type="file" name="image" id="imageInputEdit" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted text-start d-block mt-2">Leave blank to keep current image.</small>
                            </div>
                            
                            <div class="col-md-9 ps-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Property No. (Barcode ID) <span class="text-danger">*</span></label>
                                        <input type="text" name="barcode_id" id="edit_stock" class="form-control border-primary border-2 bg-light" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="edit_status" class="form-select border-secondary border-2" required>
                                            <option value="Serviceable">Serviceable</option>
                                            <option value="Unserviceable">Unserviceable / Defective</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Article (Name) <span class="text-danger">*</span></label>
                                        <input type="text" name="article" id="edit_article" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Supplier</label>
                                        <input type="text" name="supplier" id="edit_supplier" class="form-control">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea name="description" id="edit_desc" class="form-control" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Measure <span class="text-danger">*</span></label>
                                        <input type="text" name="unit_measure" id="edit_unit" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Value (₱) <span class="text-danger">*</span></label>
                                        <input type="number" name="unit_value" id="edit_value" class="form-control" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 fw-bold">Update Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="deleteAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <p class="fs-5 mb-0">Are you sure you want to delete this asset?</p>
                        <small class="text-danger">This will also delete related transactions.</small>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" id="view_details_content" style="border-radius: 10px;">
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function attachDuplicateCheck(formId) {
            const formEl = document.getElementById(formId);
            if(!formEl) return;

            formEl.addEventListener('submit', function(e) {
                e.preventDefault(); 
                const form = this;
                const formData = new FormData(form);

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                submitBtn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'duplicate') {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        
                        Swal.fire({
                            title: 'Duplicate Asset Found!',
                            text: 'This Property No. (Barcode ID) already exists in the inventory. Individual assets must have unique Property Numbers. Please change it to proceed.',
                            icon: 'warning',
                            confirmButtonText: 'Okay, let me change it',
                            confirmButtonColor: '#101954'
                        });
                    } else if (data.status === 'success') {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    Swal.fire('Error', 'An error occurred while saving the asset.', 'error');
                });
            });
        }

        attachDuplicateCheck('addAssetForm');
        attachDuplicateCheck('editForm');


        function autoFillAssetForm(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            if (!selectedOption.value) {
                document.getElementById('add_article').value = '';
                document.getElementById('add_desc').value = '';
                document.getElementById('add_supplier').value = '';
                document.getElementById('add_unit').value = '';
                document.getElementById('add_val').value = '';
                return;
            }

            document.getElementById('add_article').value = selectedOption.getAttribute('data-desc').split(' ')[0]; 
            document.getElementById('add_desc').value = selectedOption.getAttribute('data-desc');
            document.getElementById('add_supplier').value = selectedOption.getAttribute('data-supplier');
            document.getElementById('add_unit').value = selectedOption.getAttribute('data-unit') || 'Unit';
            document.getElementById('add_val').value = selectedOption.getAttribute('data-val');
        }

        function loadViewModal(id) {
            const contentArea = document.getElementById('view_details_content');
            
            new bootstrap.Modal(document.getElementById('viewAssetModal')).show();
            contentArea.innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div><p class="mt-2 mb-0">Loading...</p></div>';

            fetch(`/admin/assets/${id}/details`)
                .then(response => response.text())
                .then(data => { 
                    contentArea.innerHTML = data; 
                    
                    const barcodeEl = contentArea.querySelector('.barcode-render-modal');
                    if (barcodeEl && barcodeEl.getAttribute('data-value') !== 'N/A') {
                        JsBarcode(barcodeEl, barcodeEl.getAttribute('data-value'), {
                            format: "CODE128",
                            width: 1.5,
                            height: 40,
                            displayValue: true,
                            fontSize: 14,
                            margin: 0,
                            textMargin: 4
                        });
                    }
                });
        }

        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if(e.target.closest('button') || e.target.closest('a')) { return; }
                const id = this.getAttribute('data-id');
                loadViewModal(id);
            });
        });

        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); 
                const id = this.getAttribute('data-id');
                loadViewModal(id);
            });
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('editForm').action = `/admin/assets/${id}`;
                
                document.getElementById('edit_article').value = this.getAttribute('data-article');
                document.getElementById('edit_stock').value = this.getAttribute('data-stock');
                document.getElementById('edit_desc').value = this.getAttribute('data-desc');
                document.getElementById('edit_unit').value = this.getAttribute('data-unit');
                document.getElementById('edit_value').value = this.getAttribute('data-value');
                document.getElementById('edit_supplier').value = this.getAttribute('data-supplier');
                document.getElementById('edit_status').value = this.getAttribute('data-status');

                const currentImage = this.getAttribute('data-image');
                const preview = document.getElementById('imagePreviewEdit');
                const placeholder = document.getElementById('imagePlaceholderEdit');
                
                if (currentImage && currentImage !== '') {
                    preview.src = `/storage/assets/${currentImage}`;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                } else {
                    preview.src = '';
                    preview.style.display = 'none';
                    placeholder.style.display = 'block';
                }
                document.getElementById('imageInputEdit').value = '';
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('deleteForm').action = `/admin/assets/${id}`;
            });
        });

        document.getElementById('imageInputAdd').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreviewAdd');
            const placeholder = document.getElementById('imagePlaceholderAdd');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
                placeholder.style.display = 'block';
            }
        });

        document.getElementById('imageInputEdit').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreviewEdit');
            const placeholder = document.getElementById('imagePlaceholderEdit');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        window.addEventListener('load', function() {
            const paginationUl = document.querySelector('.custom-pagination-wrapper ul.pagination');
            if (paginationUl) {
                paginationUl.addEventListener('wheel', function(e) {
                    if (e.deltaY !== 0) {
                        e.preventDefault();
                        this.scrollLeft += (e.deltaY * 1.5);
                    }
                }, { passive: false });

                setTimeout(() => {
                    const activeLi = paginationUl.querySelector('.page-item.active');
                    if (activeLi) {
                        const ulRect = paginationUl.getBoundingClientRect();
                        const liRect = activeLi.getBoundingClientRect();
                        const scrollPos = paginationUl.scrollLeft + (liRect.left - ulRect.left) - (ulRect.width / 2) + (liRect.width / 2);
                        paginationUl.scrollLeft = scrollPos;
                        setTimeout(() => { paginationUl.style.scrollBehavior = 'smooth'; }, 50);
                    }
                }, 150); 
            }
        });
    </script>
</body>
</html>