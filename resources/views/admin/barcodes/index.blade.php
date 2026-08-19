<!DOCTYPE html>
<html lang="en">
<head>
    <title>Barcode Master List - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* --- CORE LAYOUT & DESKTOP APP-LIKE VIEW --- */
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', sans-serif; 
            overflow: hidden; /* Prevent full-page scrolling */
        }
        
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
            transition: all 0.3s; 
            height: calc(100vh - 65px); /* Fit to screen minus the top navbar */
            display: flex;
            flex-direction: column;
        }

        .title-section, .filter-card {
            flex-shrink: 0; /* Prevent these from squishing */
        }

        .filter-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }

        .table-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            display: flex;
            flex-direction: column;
            flex: 1; /* Fill the remaining screen space */
            min-height: 0; /* Crucial: stops flexbox from overflowing its container */
        }

        /* The actual scrolling container */
        .table-responsive {
            flex: 1;
            overflow-y: auto;
            overflow-x: auto;
        }

        /* Custom Scrollbar for the table */
        .table-responsive::-webkit-scrollbar { width: 8px; height: 8px; }
        .table-responsive::-webkit-scrollbar-track { background: #f4f6f9; border-radius: 4px; }
        .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Sticky Table Header */
        .table-responsive thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa; /* Matches table-light */
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
        }

        .pagination-section {
            flex-shrink: 0; /* Pins pagination to the bottom */
        }
        
        /* --- VISUAL ELEMENTS --- */
        .barcode-svg { max-height: 40px; width: auto; display: block; margin: 0 auto; }
        .barcode-text { font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; font-weight: bold; color: #101954; text-align: center; margin-top: 2px; }

        .item-row { cursor: pointer; transition: background-color 0.2s; }
        .item-row:hover { background-color: #f8f9fa !important; }

        #clearSearchBtn { cursor: pointer; transition: color 0.2s; }
        #clearSearchBtn:hover { color: #dc3545 !important; }

        /* --- Advanced Scrollable Pagination (Sticky Arrows) --- */
        #styled-pagination nav > div:not(:last-child),
        #styled-pagination p { display: none !important; }

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

        /* --- PRINT STYLES --- */
        @media print {
            .no-print, .sidebar { display: none !important; }
            body { overflow: visible !important; }
            .main-content { margin: 0; padding: 0; height: auto; display: block; }
            .card, .table-card { box-shadow: none; border: none; padding: 0; display: block; }
            .table-responsive { overflow: visible; display: block; }
            .table { width: 100%; }
        }

        /* --- MOBILE & TABLET ADAPTIVE STYLES --- */
        @media (max-width: 992px) { 
            body { overflow-y: auto; } /* Restore normal scrolling on smaller screens */
            .main-content { 
                margin-left: 0; 
                height: auto; 
                display: block; 
                padding-top: 80px; /* Avoid overlapping with mobile header */
            }
            .table-card { display: block; overflow: visible; }
            .table-responsive { overflow: visible; overflow-x: auto; }
            .table-responsive thead th { position: static; box-shadow: none; } /* Remove sticky header on mobile */
        }
    </style>
</head>
<body>

    @include('layouts.admin_header')
    @include('layouts.admin_sidebar')

    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4 no-print title-section border-bottom pb-2" style="border-color: #003366 !important;">
            <div>
                <h2 style="color: #003366; margin: 0;">
                    <i class="fas fa-barcode me-2"></i> Barcode Master List
                </h2>
                <small class="text-muted">Live directory of all system-generated inventory barcodes.</small>
            </div>
        </div>

        <div class="filter-card no-print">
            <form action="{{ url('/admin/barcodes') }}" method="GET" id="filterForm">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-muted">CATEGORY</label>
                        <select name="category" id="categoryFilter" class="form-select border-primary fw-bold" onchange="document.getElementById('filterForm').submit();">
                            <option value="all" {{ ($category ?? 'all') == 'all' ? 'selected' : '' }}>All Assets</option>
                            <option value="asset" {{ ($category ?? '') == 'asset' ? 'selected' : '' }}>Assets Only</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-bold small text-muted">SEARCH</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="searchInput" class="form-control border-start-0 border-end-0 shadow-none" placeholder="Start typing Article Name or Barcode ID to search..." value="{{ $search ?? '' }}">
                            <span class="input-group-text bg-white border-start-0 text-muted {{ empty($search) ? 'd-none' : '' }}" id="clearSearchBtn">
                                <i class="fas fa-times"></i>
                            </span>
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" onclick="promptPrintList()" class="btn btn-dark w-100"><i class="fas fa-print me-2"></i> Print List</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="barcodeTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Type</th>
                            <th style="width: 25%;">Article / Item</th>
                            <th style="width: 35%;">Description</th>
                            <th class="text-center" style="width: 20%;">Barcode</th>
                            <th class="text-end pe-4 no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barcodes as $index => $row)
                            @php
                                $badgeColor = ($row->item_type == 'asset') ? 'primary' : 'success';
                                $badgeText = strtoupper($row->item_type);
                                
                                $description = $row->description ?? '';
                                $supplier = $row->supplier ?? 'N/A';
                                $dateGen = $row->generated_at ? \Carbon\Carbon::parse($row->generated_at)->format('M d, Y') : 'N/A';
                            @endphp
                            <tr class="item-row" onclick="showItemDetails('{{ $badgeText }}', '{{ $badgeColor }}', '{{ addslashes($row->article) }}', '{{ addslashes($description) }}', '{{ $row->barcode_code }}', '{{ $dateGen }}', '{{ addslashes($supplier) }}')">
                                <td class="ps-4">
                                    <span class="badge bg-{{ $badgeColor }}">{{ $badgeText }}</span>
                                </td>
                                <td class="fw-bold text-dark">{{ $row->article }}</td>
                                <td class="text-muted small text-truncate" style="max-width: 250px;">{{ $description ?: 'No description available' }}</td>
                                <td class="text-center">
                                    <svg class="barcode-render barcode-svg" id="bc-{{ $index }}" data-value="{{ $row->barcode_code }}"></svg>
                                    <div class="barcode-text">{{ $row->barcode_code }}</div>
                                </td>
                                <td class="text-end pe-4 no-print" onclick="event.stopPropagation();">
                                    <button class="btn btn-sm btn-outline-dark" onclick="printSingle('{{ $row->barcode_code }}')" title="Print Individual Barcode">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    @if(!empty($search))
                                        No results found matching "{{ $search }}".
                                        <br>
                                        <a href="{{ url('/admin/barcodes') }}" class="btn btn-sm btn-outline-primary mt-2">Clear Search</a>
                                    @else
                                        No asset barcodes found yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3 no-print pagination-section">
                <div class="text-muted small">
                    Showing {{ $barcodes->firstItem() ?? 0 }} to {{ $barcodes->lastItem() ?? 0 }} of {{ $barcodes->total() }} results
                </div>

                <div class="d-flex align-items-center">
                    <span class="text-muted small me-2">Per page</span>
                    <form action="{{ url('/admin/barcodes') }}" method="GET" id="perPageForm">
                        <input type="hidden" name="search" value="{{ $search ?? '' }}">
                        <input type="hidden" name="category" value="{{ $category ?? 'all' }}">
                        <select name="per_page" class="form-select form-select-sm shadow-none" style="width: 70px; border-color: #101954; color: #101954; font-weight: 500;" onchange="document.getElementById('perPageForm').submit();">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                </div>

                <div class="custom-pagination-wrapper" id="styled-pagination">
                    {{ $barcodes->onEachSide(999)->appends(['per_page' => $perPage, 'search' => $search ?? '', 'category' => $category ?? 'all'])->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Render Barcode SVGs (No text attached directly to SVG, custom text div below it)
        document.querySelectorAll('.barcode-render').forEach(function(el) {
            try {
                let val = el.getAttribute('data-value');
                if(val && val.trim() !== '') {
                    JsBarcode("#" + el.id, val, {
                        format: "CODE128", 
                        width: 1.5, 
                        height: 35, 
                        displayValue: false, 
                        margin: 0
                    });
                }
            } catch(e) { console.error("Barcode format error"); }
        });

        // --- NEW: Engaging Custom Print List Logic ---
        function promptPrintList() {
            Swal.fire({
                title: '<span style="color: #101954; font-weight: bold;">Print Master List</span>',
                html: `
                    <p class="text-muted mb-4">Generate a barcode print list for registered assets:</p>
                    <div class="row g-3 px-2">
                        <div class="col-12">
                            <div class="p-4 rounded-4 border border-2 border-primary text-center shadow-sm" 
                                 style="cursor: pointer; transition: all 0.2s;" 
                                 onmouseover="this.style.backgroundColor='#cfe2ff'; this.style.transform='translateY(-3px)';" 
                                 onmouseout="this.style.backgroundColor='transparent'; this.style.transform='translateY(0)';" 
                                 onclick="executePrint('asset')">
                                <i class="fas fa-laptop fa-3x text-primary mb-3"></i>
                                <h5 class="fw-bold text-primary mb-1">Assets</h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary">Equipment</span>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonColor: '#6c757d',
                width: '500px',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        }

        // Action triggered by clicking the visual cards
        // Note: Using the staff's print-all route because it perfectly handles rendering
        window.executePrint = function(type) {
            Swal.close();
            window.open("{{ url('/barcodes/print-all') }}?type=" + type, '_blank');
        };

        // --- SWEETALERT MODAL LOGIC ---
        function showItemDetails(type, color, article, desc, code, date, supplier) {
            Swal.fire({
                html: `
                    <div class="text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="badge bg-${color}">${type}</span>
                            <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> ${date}</span>
                        </div>
                        <h4 class="fw-bold mb-1" style="color: #101954;">${article}</h4>
                        <p class="text-muted small mb-4">${desc || 'No description available'}</p>
                        
                        <div class="bg-light p-3 rounded-3 text-center mb-3 border">
                            <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(code)}&scale=3&height=10&includetext=false" style="max-height: 60px; max-width: 100%; mix-blend-mode: multiply;">
                            <div class="font-monospace fw-bold mt-2" style="font-size: 1.1rem; letter-spacing: 2px;">${code}</div>
                        </div>

                        <div class="row text-center mt-3">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Supplier</small>
                                <span class="fw-bold">${supplier || 'N/A'}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Actions</small>
                                <button class="btn btn-sm btn-outline-dark mt-1" onclick="printSingle('${code}')">
                                    <i class="fas fa-print me-1"></i> Print Label
                                </button>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Close',
                confirmButtonColor: '#101954',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            });
        }

        // --- LIVE SEARCH (DEBOUNCE & CLEAR) LOGIC ---
        let typingTimer;
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        const clearBtn = document.getElementById('clearSearchBtn');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                // Show X button if there is text
                if(this.value.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }

                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    filterForm.submit();
                }, 600); 
            });

            if (searchInput.value.length > 0) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterForm.submit();
            });
        }

        // Pagination Logic
        document.addEventListener("DOMContentLoaded", function() {
            const paginationUl = document.querySelector('.custom-pagination-wrapper ul.pagination');
            if (paginationUl) {
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

                paginationUl.addEventListener('wheel', function(e) {
                    if (e.deltaY !== 0) {
                        e.preventDefault();
                        this.scrollLeft += (e.deltaY * 1.5);
                    }
                }, { passive: false });
            }
        });

        function printSingle(code) {
            let win = window.open('', '', 'width=400,height=400');
            win.document.write('<html><head><title>Print Barcode</title></head><body style="text-align:center; padding-top:20px;">');
            let svg = document.querySelector(`svg[data-value="${code}"]`);
            if(svg) {
                let xml = new XMLSerializer().serializeToString(svg);
                let svg64 = btoa(xml);
                let image64 = 'data:image/svg+xml;base64,' + svg64;
                win.document.write('<img src="' + image64 + '" style="max-width:100%;" />');
                win.document.write('<h2 style="font-family:sans-serif; margin-top:10px;">'+code+'</h2>');
                win.document.write('<script>setTimeout(function(){ window.print(); window.close(); }, 500);<\/script>');
            } else {
                win.document.write('Error generating barcode image.');
            }
            win.document.write('</body></html>');
            win.document.close();
        }
    </script>
</body>
</html>