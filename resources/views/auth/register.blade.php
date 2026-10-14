
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account - InvoiceFlow</title>

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

        <h1>Create your account</h1>

        <p class="auth-description">
            Get started with InvoiceFlow and manage your
            customers, invoices, and payments in one place.
        </p>

        @if ($errors->any())
            <div class="auth-error" role="alert">
                <strong>Please correct the following errors:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- NAME -->
            <div class="auth-field">
                <label for="name">Full name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter your full name"
                    autocomplete="name"
                    required
                    autofocus
                    aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                >

                @error('name')
                <span class="auth-field-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- EMAIL -->
            <div class="auth-field">
                <label for="email">Email address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                >

                @error('email')
                <span class="auth-field-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- PASSWORD -->
            <div class="auth-field">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    autocomplete="new-password"
                    required
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                >

                <span class="auth-hint">
                    Choose a strong password to protect your account.
                </span>

                @error('password')
                <span class="auth-field-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="auth-field">
                <label for="password_confirmation">
                    Confirm password
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Re-enter your password"
                    autocomplete="new-password"
                    required
                >
            </div>

            <!-- SUBMIT -->
            <button type="submit" class="auth-submit">
                Create Account
            </button>

        </form>

        <div class="auth-footer">
            Already have an account?

            <a href="{{ route('login') }}" class="auth-link">
                Log in
            </a>
        </div>

    </div>
</main>

</body>
</html>
