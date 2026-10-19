
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'InvoiceFlow')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="app-layout">

    <!-- Mobile sidebar overlay -->
    <div
        class="sidebar-overlay"
        id="sidebar-overlay"
        aria-hidden="true"
    ></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="app-sidebar">

        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <span class="brand-icon">IF</span>
                <span>InvoiceFlow</span>
            </a>

            <button
                type="button"
                class="sidebar-close"
                id="sidebar-close"
                aria-label="Close navigation menu"
            >
                &times;
            </button>
        </div>

        <div class="sidebar-section-label">WORKSPACE</div>

        <nav class="sidebar-nav" aria-label="Main navigation">

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">▦</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('customers.index') }}"
               class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
               @if(request()->routeIs('customers.*')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">♙</span>
                <span>Customers</span>
            </a>

            <a href="{{ route('invoices.index') }}"
               class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"
               @if(request()->routeIs('invoices.*')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">▤</span>
                <span>Invoices</span>
            </a>


            <a href="{{ route('business-profile.edit') }}"
               class="sidebar-link {{ request()->routeIs('business-profile.*') ? 'active' : '' }}"
               @if(request()->routeIs('business-profile.*')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">▣</span>
                <span>Business Profile</span>
            </a>


        </nav>

        <div class="sidebar-bottom">
            @auth
                <div class="sidebar-user">
                    <div class="user-avatar" aria-hidden="true">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="user-details">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->email }}</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="logout-button">
                        Sign out
                    </button>
                </form>
            @endauth
        </div>

    </aside>

    <!-- Main application -->
    <div class="app-main">

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="mobile-menu-button"
                    id="mobile-menu-button"
                    aria-label="Open navigation menu"
                    aria-controls="app-sidebar"
                    aria-expanded="false"
                >
                    <span class="menu-line"></span>
                    <span class="menu-line"></span>
                    <span class="menu-line"></span>
                </button>

                <span class="topbar-brand">InvoiceFlow</span>
            </div>

            <div class="topbar-right">
                @auth
                    <span class="topbar-username">
                        {{ auth()->user()->name }}
                    </span>

                    <div class="topbar-avatar" aria-hidden="true">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endauth
            </div>

        </header>

        <main class="page-content" id="main-content">
            @yield('content')
        </main>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const menuButton = document.getElementById('mobile-menu-button');
        const closeButton = document.getElementById('sidebar-close');

        const mobileQuery = window.matchMedia('(max-width: 768px)');

        function openSidebar() {
            sidebar.classList.add('sidebar-open');
            overlay.classList.add('overlay-visible');

            menuButton.setAttribute('aria-expanded', 'true');
            menuButton.setAttribute('aria-label', 'Close navigation menu');

            document.body.classList.add('sidebar-mobile-open');
            closeButton.focus();
        }

        function closeSidebar() {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('overlay-visible');

            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Open navigation menu');

            document.body.classList.remove('sidebar-mobile-open');
        }

        menuButton.addEventListener('click', function () {
            if (sidebar.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        closeButton.addEventListener('click', function () {
            closeSidebar();
            menuButton.focus();
        });

        overlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (event) {
            if (
                event.key === 'Escape' &&
                sidebar.classList.contains('sidebar-open')
            ) {
                closeSidebar();
                menuButton.focus();
            }
        });

        sidebar.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', closeSidebar);
        });

        mobileQuery.addEventListener('change', function () {
            closeSidebar();
        });
    });
</script>

</body>
</html>
