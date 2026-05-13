<style>
    .sidebar::-webkit-scrollbar {
        display: none;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #101954; /* DepEd Blue */
        color: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1050;
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: background-color 0.2s;
    }
    .sidebar-header:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    .sidebar-header img { width: 60px; height: 60px; margin-bottom: 10px; }
    .sidebar-header h5 { font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: 1px; }

    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 15px 20px; font-size: 1rem;
        border-left: 4px solid transparent;
        transition: all 0.2s; display: flex; align-items: center;
        text-decoration: none; position: relative; 
    }
    .nav-link i { width: 30px; font-size: 1.1rem; }
    .nav-link:hover, .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff; border-left-color: #fc1111;
    }

    .submenu .nav-link {
        padding: 10px 20px 10px 45px;
        font-size: 0.9rem;
        background-color: rgba(0, 0, 0, 0.2);
    }
    .submenu .nav-link.active {
        background-color: rgba(252, 17, 17, 0.2);
        border-left-color: #fc1111;
    }
    .nav-link[aria-expanded="true"] {
        background-color: rgba(255, 255, 255, 0.05); color: #fff;
    }

    .menu-arrow {
        position: absolute; right: 20px; top: 50%;
        transform: translateY(-50%); transition: transform 0.3s ease;
        font-size: 0.8rem; text-align: center; color : rgba(255, 255, 255, 0.8);
    }
    a[aria-expanded="true"] .menu-arrow { transform: translateY(-50%) rotate(180deg); }

    .sidebar-footer {
        margin-top: auto; padding: 20px; text-align: center;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.5); font-size: 0.85rem;
    }

    /* --- MOBILE RESPONSIVE STYLES --- */
    .mobile-menu-btn {
        position: fixed;
        top: 12px;
        left: 15px;
        z-index: 1051; 
        background: transparent;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        font-size: 1.5rem;
        display: none;
        cursor: pointer;
        transition: 0.2s;
    }
    .mobile-menu-btn:hover { background: rgba(255, 255, 255, 0.1); }

    .sidebar-backdrop {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.5);
        z-index: 1045; 
        display: none; opacity: 0;
        transition: opacity 0.3s;
    }
    
    .sidebar-backdrop.active { display: block; opacity: 1; }

    @media (max-width: 768px) {
        .sidebar { 
            left: -260px; 
            box-shadow: 5px 0 15px rgba(0,0,0,0.3);
        }
        .sidebar.active { left: 0; } 
        .mobile-menu-btn { display: block; }
    }
</style>

<button class="mobile-menu-btn no-print" id="mobileMenuBtn">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-backdrop no-print" id="sidebarBackdrop"></div>

<div class="sidebar" id="mainSidebar">
    <a href="{{ url('/') }}" style="text-decoration: none; color: inherit;">
        <div class="sidebar-header" title="Go to Landing Page">
            <img src="{{ asset('assets/images/depedRovCirc.png') }}" alt="Logo">
            <h5>AMS HEAD</h5>
        </div>
    </a>

    <nav class="nav flex-column mt-3">
        <a href="{{ url('/admin/dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt fa-fw"></i> Dashboard
        </a>
        
        <div class="nav-item">
            @php
                $isInventoryActive = request()->is('admin/assets*') || request()->is('admin/supplies*');
            @endphp
            
            <a href="#inventorySubmenu" 
               data-bs-toggle="collapse" 
               role="button"
               id="inventoryToggle"
               class="nav-link {{ $isInventoryActive ? 'active' : '' }}" 
               aria-expanded="{{ $isInventoryActive ? 'true' : 'false' }}">
                <i class="fas fa-boxes fa-fw"></i> Inventory
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            
            <div class="collapse submenu {{ $isInventoryActive ? 'show' : '' }}" id="inventorySubmenu">
                <a href="{{ url('/admin/assets') }}" class="nav-link {{ request()->is('admin/assets*') ? 'active' : '' }}">
                    <i class="fas fa-laptop fa-fw"></i> Assets List
                </a>
                <a href="{{ url('/admin/supplies') }}" class="nav-link {{ request()->is('admin/supplies*') ? 'active' : '' }}">
                    <i class="fas fa-box-open fa-fw"></i> Supplies List
                </a>
            </div>
        </div>

        <a href="{{ url('/admin/po') }}" class="nav-link {{ request()->is('admin/po*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice fa-fw"></i> Delivery Orders
        </a>

        <a href="{{ url('/admin/requests') }}" class="nav-link {{ request()->is('admin/requests*') ? 'active' : '' }}">
            <i class="fas fa-file-signature fa-fw"></i> Requests (RIS)
        </a>
        
        <a href="{{ url('/admin/barcodes') }}" class="nav-link {{ request()->is('admin/barcodes*') ? 'active' : '' }}">
            <i class="fas fa-barcode fa-fw"></i> Barcode List
        </a>
        
        <a href="{{ url('/admin/transactions') }}" class="nav-link {{ request()->is('admin/transactions*') ? 'active' : '' }}">
            <i class="fas fa-history fa-fw"></i> Transactions
        </a>

        <a href="{{ url('/admin/reports') }}" class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar fa-fw"></i> Reports
        </a>
        
        <a href="{{ url('/admin/users') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
            <i class="fas fa-users-cog fa-fw"></i> User Management
        </a>

        <a href="{{ url('/admin/activity-logs') }}" class="nav-link {{ request()->is('admin/activity-logs*') ? 'active' : '' }}">
            <i class="fas fa-user-clock fa-fw"></i> System Logs
        </a>

        <a href="{{ url('/admin/settings') }}" class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
            <i class="fas fa-cog fa-fw"></i> System Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        &copy; {{ date('Y') }} DepEd AMS.
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- Mobile Sidebar Toggle Logic ---
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('mainSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (mobileBtn && sidebar && backdrop) {
            function toggleSidebar() {
                sidebar.classList.toggle('active');
                if (sidebar.classList.contains('active')) {
                    backdrop.style.display = 'block';
                    setTimeout(() => backdrop.style.opacity = '1', 10);
                } else {
                    backdrop.style.opacity = '0';
                    setTimeout(() => backdrop.style.display = 'none', 300);
                }
            }
            mobileBtn.addEventListener('click', toggleSidebar);
            backdrop.addEventListener('click', toggleSidebar); 
        }

        // Submenu memory logic
        const inventoryToggle = document.getElementById('inventoryToggle');
        const inventorySubmenu = document.getElementById('inventorySubmenu');

        if(inventoryToggle && inventorySubmenu) {
            let isInventoryActive = {{ $isInventoryActive ? 'true' : 'false' }};
            let savedState = sessionStorage.getItem('inventoryMenuOpen');

            if (!isInventoryActive && savedState === 'true') {
                inventorySubmenu.classList.add('show');
                inventoryToggle.setAttribute('aria-expanded', 'true');
            }

            inventorySubmenu.addEventListener('shown.bs.collapse', function () {
                inventoryToggle.setAttribute('aria-expanded', 'true');
                sessionStorage.setItem('inventoryMenuOpen', 'true');
            });

            inventorySubmenu.addEventListener('hidden.bs.collapse', function () {
                inventoryToggle.setAttribute('aria-expanded', 'false');
                sessionStorage.setItem('inventoryMenuOpen', 'false');
            });
        }
    });

    let idleTimer;
    const idleTimeLimit = 120000; 

    function resetIdleTimer() {
        clearTimeout(idleTimer);
        idleTimer = setTimeout(() => {
            window.location.href = "{{ url('/idle-screen') }}";
        }, idleTimeLimit);
    }

    const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
    activityEvents.forEach(event => {
        document.addEventListener(event, resetIdleTimer, true);
    });

    resetIdleTimer();
</script>