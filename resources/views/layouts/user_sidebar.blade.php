<style>
    .sidebar {
        background-color: #101954;
        min-height: 100vh;
        color: white;
        display: flex;
        flex-direction: column;
        position: fixed; 
        width: 250px;
        z-index: 1050;
        top: 0;
        left: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
    .sidebar-header img {
        width: 60px;
        height: 60px;
        margin-bottom: 10px;
    }
    .sidebar-header h5 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: 1px;
    }

    .nav-menu {
        flex-grow: 1;
        margin-top: 20px;
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.7);
        padding: 15px 25px;
        text-decoration: none;
        display: block;
        transition: 0.3s;
    }

    .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .nav-link:hover, .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border-left: 4px solid #0d6efd;
    }

    .sidebar-footer {
        margin-top: auto;
        padding: 20px;
        text-align: center;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.85rem;
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
            <h5>DIVISION USER</h5>
        </div>
    </a>
    
    <div class="nav-menu">
        <a href="{{ url('/user/dashboard') }}" class="nav-link {{ request()->is('user/dashboard*') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        
        <a href="{{ url('/user/ris/create') }}" class="nav-link {{ request()->is('user/ris/create*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-signature"></i> RIS
        </a>
        
        <a href="{{ url('/user/ris/history') }}" class="nav-link {{ request()->is('user/ris/history*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left"></i> RIS History
        </a>
    
    </div>

    <div class="sidebar-footer">
        &copy; {{ date('Y') }} DepEd AMS.
    </div>
</div>

<script>
    // --- Mobile Sidebar Toggle Logic ---
    document.addEventListener('DOMContentLoaded', function() {
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