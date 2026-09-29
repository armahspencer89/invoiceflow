<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InvoiceFlow</title>
</head>
<body>

<h1>Login to InvoiceFlow</h1>

<p>Sign in to manage your invoices.</p>

@if ($errors->any())
    <div>
        <strong>There was a problem:</strong>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="/login">

    @csrf

    <div>
        <label for="email">Email</label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
        >
    </div>

    <div>
        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >
    </div>

    <div>
        <label>
            <input
                type="checkbox"
                name="remember"
                value="1"
            >

            Remember me
        </label>
    </div>

    <button type="submit">Login</button>

</form>

<p>
    <a href="/register">Create an account</a>
</p>

<p>
    <a href="/forgot-password">Forgot your password?</a>
</p>

</body>
</html>
