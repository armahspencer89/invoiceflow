<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - InvoiceFlow</title>
</head>
<body>

<h1>InvoiceFlow Dashboard</h1>

<p>Welcome, {{ auth()->user()->name }}!</p>

<p>You are successfully authenticated.</p>

<form method="POST" action="/logout">
    @csrf
    <button type="submit">Logout</button>
</form>

</body>
</html>
