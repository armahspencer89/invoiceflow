
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

    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <span class="brand-icon">IF</span>
            <span>InvoiceFlow</span>
        </a>

        <div class="sidebar-section-label">WORKSPACE</div>

        <nav class="sidebar-nav" aria-label="Main navigation">

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">▦</span>
                Dashboard
            </a>

            <a href="{{ route('customers.index') }}"
               class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <span class="nav-icon">♙</span>
                Customers
            </a>

            <a href="{{ route('invoices.index') }}"
               class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <span class="nav-icon">▤</span>
                Invoices
            </a>

        </nav>

        <div class="sidebar-bottom">
            @auth
                <div class="sidebar-user">
                    <div class="user-avatar">
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
                <span class="topbar-brand">InvoiceFlow</span>
            </div>

            <div class="topbar-right">
                @auth
                    <span class="topbar-username">
                        {{ auth()->user()->name }}
                    </span>
                    <div class="topbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endauth
            </div>
        </header>

        <main class="page-content">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
