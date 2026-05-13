<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - DepEd AMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .table td {
            vertical-align: middle;
            font-size: 0.95rem;
        }

        /* Action Badges */
        .action-login { background-color: #cfe2ff; color: #084298; border: 1px solid #b6d4fe; }
        .action-create { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .action-update { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .action-delete { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .action-default { background-color: #e2e3e5; color: #41464b; border: 1px solid #d3d6d8; }

        /* --- Advanced Scrollable Pagination (Sticky Arrows) --- */
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
        
        @media (max-width: 768px) { 
            .main-content { margin-left: 0; height: auto; overflow: visible; } 
            body { overflow: visible; height: auto; }
            .table-container { min-height: 500px; }
            .mobile-stack { width: 100% !important; max-width: 100% !important; margin-bottom: 10px; }
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
                    <i class="fas fa-user-shield text-primary me-2"></i>System Activity Logs
                </h3>
                <small class="text-muted">Monitor user logins, creations, updates, and deletions across the system.</small>
            </div>
            <button class="btn btn-dark shadow-sm d-none d-md-block" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Print Logs
            </button>
        </div>

        <div class="table-container">
            
            <form action="{{ url('/admin/activity-logs') }}" method="GET" id="filterForm" class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div class="d-flex flex-wrap gap-2 flex-grow-1">
                    <div class="input-group shadow-sm mobile-stack" style="max-width: 350px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" id="logSearchInput" class="form-control border-start-0 ps-0" placeholder="Search user, action, or details..." value="{{ request('search') }}">
                    </div>
                    
                    <select name="action_filter" class="form-select shadow-sm mobile-stack" style="max-width: 180px;" onchange="document.getElementById('filterForm').submit();">
                        <option value="All" {{ request('action_filter') == 'All' ? 'selected' : '' }}>All Actions</option>
                        <option value="Login" {{ request('action_filter') == 'Login' ? 'selected' : '' }}>Login</option>
                        <option value="Created" {{ request('action_filter') == 'Created' ? 'selected' : '' }}>Created</option>
                        <option value="Updated" {{ request('action_filter') == 'Updated' ? 'selected' : '' }}>Updated</option>
                        <option value="Deleted" {{ request('action_filter') == 'Deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="Logout" {{ request('action_filter') == 'Logout' ? 'selected' : '' }}>Logout</option>
                    </select>

                    <input type="date" name="date" value="{{ request('date') }}" class="form-control shadow-sm mobile-stack" style="max-width: 180px;" onchange="document.getElementById('filterForm').submit();">
                </div>

                @if(request('search') || (request('action_filter') && request('action_filter') !== 'All') || request('date'))
                    <a href="{{ url('/admin/activity-logs') }}" class="btn btn-outline-danger btn-sm fw-bold shadow-sm mobile-stack">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date & Time</th>
                            <th>User</th>
                            <th class="text-center">Action</th>
                            <th style="min-width: 300px;">Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $actionLower = strtolower($log->action);
                                $badgeClass = 'action-default';
                                $icon = 'fa-info-circle';

                                if(str_contains($actionLower, 'login')) { $badgeClass = 'action-login'; $icon = 'fa-sign-in-alt'; }
                                elseif(str_contains($actionLower, 'create')) { $badgeClass = 'action-create'; $icon = 'fa-plus-circle'; }
                                elseif(str_contains($actionLower, 'update')) { $badgeClass = 'action-update'; $icon = 'fa-edit'; }
                                elseif(str_contains($actionLower, 'delete')) { $badgeClass = 'action-delete'; $icon = 'fa-trash-alt'; }
                                elseif(str_contains($actionLower, 'logout')) { $badgeClass = 'action-default'; $icon = 'fa-sign-out-alt'; }
                            @endphp
                            <tr>
                                <td class="ps-3 text-nowrap">
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; flex-shrink: 0;">
                                            {{ strtoupper(substr($log->user->firstname ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-dark" style="line-height: 1.2;">{{ $log->user ? $log->user->firstname . ' ' . $log->user->lastname : 'System / Deleted User' }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $log->user->role ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge rounded-pill {{ $badgeClass }} px-3 py-2">
                                        <i class="fas {{ $icon }} me-1"></i> {{ $log->action }}
                                    </span>
                                </td>
                                <td class="text-secondary">{{ $log->description }}</td>
                                <td class="font-monospace text-muted small">{{ $log->ip_address ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted border-bottom-0">
                                    <i class="fas fa-history fa-3x mb-3 opacity-25 d-block"></i>
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} records
                </div>

                <div class="d-flex align-items-center">
                    <span class="text-muted small me-2">Per page</span>
                    <form action="{{ url('/admin/activity-logs') }}" method="GET" id="perPageForm">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('action_filter')) <input type="hidden" name="action_filter" value="{{ request('action_filter') }}"> @endif
                        @if(request('date')) <input type="hidden" name="date" value="{{ request('date') }}"> @endif
                        
                        <select name="per_page" class="form-select form-select-sm shadow-none" style="width: 70px; border-color: #101954; color: #101954; font-weight: 500;" onchange="document.getElementById('perPageForm').submit();">
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>

                <div class="custom-pagination-wrapper" id="scrollablePagination">
                    {{ $logs->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-search logic (Debounce)
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('logSearchInput');
            const filterForm = document.getElementById('filterForm');
            let typingTimer;

            if(searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
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
        });

        // Advanced Pagination Scroll & Auto-Center
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