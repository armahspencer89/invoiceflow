<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'InvoiceFlow')</title>
</head>
<body>
<header>
    <nav>
        <a href="{{ route('dashboard') }}">InvoiceFlow</a>

        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('customers.index') }}">Customers</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @endauth
    </nav>
</header>

<main>
    @yield('content')
</main>
</body>
</html>
