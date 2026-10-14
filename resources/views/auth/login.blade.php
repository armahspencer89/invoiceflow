
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Log In - InvoiceFlow</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="auth-page">

<header class="auth-header">
    <a href="{{ url('/') }}" class="auth-brand">
        <span class="auth-brand-icon">IF</span>
        <span>InvoiceFlow</span>
    </a>
</header>

<main class="auth-main">
    <div class="auth-card">

        <h1>Welcome back</h1>

        <p class="auth-description">
            Log in to your InvoiceFlow account to manage
            customers, invoices, and payments.
        </p>

        @if (session('status'))
            <div class="auth-status" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-error" role="alert">
                <strong>There was a problem:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="auth-field">
                <label for="email">Email address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    autofocus
                >
            </div>

            <div class="auth-field">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <div class="auth-options">
                <label class="auth-remember">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    Remember me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="auth-link">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="auth-submit">
                Log In
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account?

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="auth-link">
                    Create an account
                </a>
            @endif
        </div>

    </div>
</main>

</body>
</html>
