<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Transaction History - Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', sans-serif; 
            overflow: hidden; 
        }
        
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
            transition: all 0.3s; 
            height: calc(100vh - 65px); 
            display: flex;
            flex-direction: column;
        }

        .title-section, .search-box, .alert {
            flex-shrink: 0; 
        }

        .search-box {
            background: white;
            border-radius: 10px;
        }

        .history-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            display: flex;
            flex-direction: column;
            flex: 1; 
            min-height: 0; 
            padding: 20px 20px 0 20px;
        }

        .table-responsive {
            flex: 1;
            overflow-y: auto;
            overflow-x: auto;
            margin-bottom: 0;
            border-bottom: 1px solid #dee2e6;
        }

        .table-responsive::-webkit-scrollbar { width: 8px; height: 8px; }
        .table-responsive::-webkit-scrollbar-track { background: #f4f6f9; border-radius: 4px; }
        .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #e9ecef;
            color: #495057;
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .pagination-section {
            flex-shrink: 0; 
            padding-bottom: 20px;
        }
        
        .badge-in { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .badge-added { background-color: #cfe2ff; color: #084298; border: 1px solid #b6d4fe; } 
        .badge-out { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .badge-issued { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
        .badge-returned { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .badge-transferred { background-color: #e2d9f3; color: #432874; border: 1px solid #c5b3e6; }
        
        .text-small { font-size: 0.9rem; }

        .tx-row { cursor: pointer; transition: background-color 0.2s; }
        .tx-row:hover { background-color: #f8f9fa !important; }

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

        .modal { z-index: 1060 !important; }
        .modal-backdrop { z-index: 1055 !important; }

        .print-heading { display: none; }

        @media (max-width: 992px) { 
            body { overflow-y: auto; } 
            .main-content { 
                margin-left: 0; 
                height: auto; 
                display: block; 
                padding-top: 80px; 
            }
            .history-card { display: block; overflow: visible; padding: 20px; }
            .table-responsive { overflow: visible; overflow-x: auto; border-bottom: none; }
            .table thead th { position: static; box-shadow: none; } 
        }

        @media print {
            .no-print, .sidebar { display: none !important; }
            .print-heading { display: block; text-align: center; border-bottom: 2px solid #101954; padding-bottom: 10px; margin-bottom: 14px; }
            .print-heading h1 { margin: 0; color: #101954; font-size: 18pt; letter-spacing: 0.04em; }
            .print-heading p { margin: 3px 0 0; color: #555; font-size: 9pt; }
            body { overflow: visible !important; }
            .main-content { margin: 0; padding: 0; height: auto; display: block; }
            .card, .history-card { box-shadow: none; border: none; padding: 0; display: block; }
            .table-responsive { overflow: visible; display: block; border-bottom: none; }
            .table { width: 100%; border-collapse: collapse; font-size: 8pt; }
            .table th, .table td { border: 1px solid #adb5bd !important; padding: 6px 7px !important; vertical-align: top; }
            .table thead th { position: static; background: #e9ecef !important; color: #111; box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .table tbody tr:nth-child(even) { background: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    @include('layouts.header')
    @include('layouts.sidebar')

    <div class="main-content">
        <div class="print-heading">
            <h1>Inventory Transaction History</h1>
            <p>Department of Education | Generated {{ now()->format('F d, Y h:i A') }}</p>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4 no-print title-section">
            <div>
                <h3 class="fw-bold text-dark mb-0"><i class="fas fa-history text-primary me-2"></i>Transaction History</h3>
                <small class="text-muted">Track all inventory movements (In, Out, and New Items).</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-dark shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Log
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3 search-box no-print">
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search by item name, barcode, or remarks...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="typeFilter" class="form-select border-primary">
                            <option value="all">All Transaction Types</option>
                            <option value="IN">Stock IN</option>
                            <option value="OUT">Stock OUT</option>
                            <option value="ADDED">Newly Added</option>
                            <option value="ISSUED">Issued / Borrowed</option>
                            <option value="RETURNED">Returned to Inventory</option>
                            <option value="TRANSFERRED">Transferred</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card history-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="transactionTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Date & Time</th>
                            <th class="text-center">Type</th>
                            <th>Item / Release Batch</th>
                            <th>Category</th>
                            <th>Supplier / Requested By</th>
                            <th class="text-center">Qty</th>
                            <th>Remarks</th>
                            <th class="text-center no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $row)
                            @php
                                $isGrouped = $row->item_count > 1 || (strtoupper($row->transaction_type) == 'OUT' && str_starts_with($row->remarks, 'RIS'));
                                
                                $displayName = $isGrouped ? 'RIS Batch Release' : ($row->item_name ?? 'Unknown Item');
                                $displayCode = $isGrouped ? $row->remarks : ($row->item_code ?? '-');
                                $displayCategory = $isGrouped ? 'MULTIPLE' : (strtolower($row->raw_item_type) == 'supplies' ? 'SUPPLY' : 'ASSET');
                                
                                $badgeClass = 'badge-in';
                                $icon = 'fa-arrow-down';
                                $typeUpper = strtoupper($row->transaction_type);
                                
                                if ($typeUpper == 'OUT') {
                                    $badgeClass = 'badge-out';
                                    $icon = 'fa-arrow-up';
                                } elseif ($typeUpper == 'ADDED') {
                                    $badgeClass = 'badge-added';
                                    $icon = 'fa-plus';
                                } elseif ($typeUpper == 'ISSUED') {
                                    $badgeClass = 'badge-issued';
                                    $icon = 'fa-hand-holding';
                                } elseif ($typeUpper == 'RETURNED') {
                                    $badgeClass = 'badge-returned';
                                    $icon = 'fa-rotate-left';
                                } elseif ($typeUpper == 'TRANSFERRED') {
                                    $badgeClass = 'badge-transferred';
                                    $icon = 'fa-right-left';
                                }
                                
                                if ($typeUpper == 'OUT') {
                                    $displaySupplier = !empty($row->requested_by) ? $row->requested_by : 'Division Request';
                                } else {
                                    $displaySupplier = $isGrouped ? '-' : (!empty($row->supplier) ? $row->supplier : '-');
                                }

                                $dateObj = \Carbon\Carbon::parse($row->date_time);
                            @endphp
                            
                            <tr class="tx-row" 
                                data-type="{{ $typeUpper }}"
                                data-is-grouped="{{ $isGrouped ? 'true' : 'false' }}"
                                data-item="{{ $displayName }}"
                                data-code="{{ $displayCode }}"
                                data-qty="{{ $row->quantity }}"
                                data-date="{{ $dateObj->format('F d, Y h:i A') }}"
                                data-remarks="{{ $row->remarks }}"
                                data-supplier="{{ $displaySupplier }}"
                                data-items="{{ $row->grouped_items }}"
                                data-codes="{{ $row->grouped_codes }}"
                                data-qtys="{{ $row->grouped_qtys }}"
                                data-totals="{{ $row->grouped_totals }}"
                                onclick="openTxModal(this)">
                                
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $dateObj->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $dateObj->format('h:i A') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $badgeClass }} px-3 py-2">
                                        <i class="fas {{ $icon }} me-1"></i> {{ $typeUpper }}
                                    </span>
                                </td>
                                <td class="searchable">
                                    <span class="fw-bold d-block text-dark">
                                        {{ $displayName }}
                                    </span>
                                    <small class="text-muted font-monospace">
                                        {{ $displayCode }}
                                    </small>
                                </td>
                                <td>
                                    @if($displayCategory == 'SUPPLY')
                                        <span class="badge bg-success border-0 px-2 py-1">SUPPLY</span>
                                    @elseif($displayCategory == 'ASSET')
                                        <span class="badge bg-primary border-0 px-2 py-1">ASSET</span>
                                    @else
                                        <span class="badge bg-secondary border-0 px-2 py-1">MULTIPLE</span>
                                    @endif
                                </td>
                                <td class="text-muted searchable fw-semibold">
                                    {{ $displaySupplier }}
                                </td>
                                <td class="text-center fw-bold fs-6">
                                    {{ $row->quantity }}
                                </td>
                                <td class="text-small text-secondary searchable" style="max-width: 200px;">
                                    {{ $row->remarks }}
                                </td>
                                <td class="text-center no-print">
                                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openTxModal(this.closest('tr'))">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-2x mb-3 text-secondary opacity-50"></i>
                                    <p>No transaction history found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 mt-3 no-print pagination-section px-1">
                <div class="text-muted small">
                    Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} results
                </div>

                <div class="d-flex align-items-center">
                    <span class="text-muted small me-2">Per page</span>
                    <form action="{{ url('/transactions') }}" method="GET" id="perPageForm">
                        <select name="per_page" class="form-select form-select-sm shadow-none" style="width: 70px; border-color: #101954; color: #101954; font-weight: 500;" onchange="document.getElementById('perPageForm').submit();">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                </div>

                <div class="custom-pagination-wrapper" id="styled-pagination">
                    {{ $transactions->onEachSide(999)->appends(['per_page' => $perPage])->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewTxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i> Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h4 id="m_item" class="fw-bold text-dark mb-0"></h4>
                        <div id="m_code" class="text-muted font-monospace mt-1"></div>
                    </div>
                    
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Transaction Type</span>
                            <span id="m_type" class="fw-bold"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Total Quantity</span>
                            <span id="m_qty" class="fw-bold fs-5"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Date & Time</span>
                            <span id="m_date" class="fw-bold text-dark"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted" id="m_supplier_label">Supplier</span>
                            <span id="m_supplier" class="fw-bold text-dark text-end" style="max-width: 65%; word-break: break-word;"></span>
                        </li>
                    </ul>
                    
                    <div id="m_items_list_container" style="display: none;">
                        <span class="text-muted d-block mb-2 text-uppercase small fw-bold">Items Released</span>
                        <div id="m_items_list" class="mb-3 border rounded-3 p-1 bg-light" style="max-height: 250px; overflow-y: auto;">
                            </div>
                    </div>

                    <div class="px-0 pt-2 border-top">
                        <span class="text-muted d-block mb-2 mt-2">Remarks / Reference</span>
                        <div id="m_remarks" class="p-3 bg-light rounded-3 border text-dark font-monospace text-center"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 justify-content-center bg-light rounded-bottom">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Close Window</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal logic
        window.openTxModal = function(rowElement) {
            let $row = $(rowElement);
            let isGrouped = $row.data('is-grouped');
            let type = $row.data('type');
            
            $('#m_item').text($row.data('item'));
            $('#m_code').text($row.data('code'));
            $('#m_qty').text($row.data('qty'));
            $('#m_date').text($row.data('date'));
            $('#m_remarks').text($row.data('remarks') || 'No remarks provided.');
            
            $('#m_supplier_label').text(type === 'OUT' ? 'Requested By' : 'Supplier');
            $('#m_supplier').text($row.data('supplier') || '-');
            
            let typeClass = type === 'IN' ? 'text-success' : (type === 'OUT' ? 'text-danger' : 'text-primary');
            $('#m_type').html(`<span class="${typeClass} fw-bold">${type}</span>`);
            
            if (isGrouped) {
                let itemsStr = String($row.data('items') || '');
                let codesStr = String($row.data('codes') || '');
                let qtysStr = String($row.data('qtys') || '');
                let totalsStr = String($row.data('totals') || ''); // The newly passed 47/51 ratio strings

                let items = itemsStr.split('||');
                let codes = codesStr.split('||');
                let qtys = qtysStr.split('||');
                let totals = totalsStr.split('||');
                
                let listHtml = '<ul class="list-group list-group-flush">';
                for(let i=0; i<items.length; i++) {
                    if(!items[i]) continue;
                    
                    let deductionDisplay = totals[i] && totals[i] !== '0 / 0' 
                                            ? `<div class="text-success fw-bold" style="font-size: 0.75rem;"><i class="fas fa-box"></i> Left: ${totals[i]}</div>`
                                            : '';

                    listHtml += `<li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 bg-transparent border-bottom">
                        <div>
                            <span class="fw-bold d-block text-dark" style="font-size: 0.95rem;">${items[i]}</span>
                            <small class="text-muted font-monospace" style="font-size: 0.75rem;">${codes[i] || '-'}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-dark rounded-pill shadow-sm px-3 mb-1">Qty: ${qtys[i] || 1}</span>
                            ${deductionDisplay}
                        </div>
                    </li>`;
                }
                listHtml += '</ul>';
                
                $('#m_items_list').html(listHtml);
                $('#m_items_list_container').show();
            } else {
                $('#m_items_list_container').hide();
            }
            
            $('#viewTxModal').modal('show');
        }

        $(document).ready(function() {
            // JS Filter and Search Logic
            function filterTable() {
                let search = $('#searchInput').val().toLowerCase();
                let type = $('#typeFilter').val();

                $('.tx-row').each(function() {
                    let rowType = $(this).data('type');
                    let text = $(this).find('.searchable').text().toLowerCase();
                    
                    let matchSearch = text.indexOf(search) > -1;
                    let matchType = (type === 'all' || rowType === type);

                    if (matchSearch && matchType) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            $('#searchInput').on('keyup', filterTable);
            $('#typeFilter').on('change', filterTable);
        });

        // Improved Pagination Logic
        document.addEventListener("DOMContentLoaded", function() {
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
                        setTimeout(() => {
                            paginationUl.style.scrollBehavior = 'smooth';
                        }, 50);
                    }
                }, 150); 
            }
        });
    </script>
</body>
</html>