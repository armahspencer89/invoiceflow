
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>InvoiceFlow — Simple Invoicing for Small Businesses</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="landing-page">

<header class="landing-nav">
    <div class="landing-container landing-nav-inner">

        <a href="{{ url('/') }}" class="landing-logo">
            <span class="landing-logo-icon">IF</span>
            <span>InvoiceFlow</span>
        </a>

        <nav class="landing-nav-actions">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="landing-btn landing-btn-primary">
                    Dashboard
                </a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="landing-btn landing-btn-outline">
                        Log In
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="landing-btn landing-btn-primary">
                        Get Started
                    </a>
                @endif
            @endauth
        </nav>

    </div>
</header>

<main>

    <!-- HERO SECTION -->
    <section class="landing-hero">
        <div class="landing-container landing-hero-content">

            <span class="landing-eyebrow">
                Simple invoicing, less paperwork
            </span>

            <h1>
                Manage your invoices.
                <span>Focus on your business.</span>
            </h1>

            <p class="landing-hero-description">
                InvoiceFlow helps freelancers and small businesses
                organize customers, create invoices, track payments,
                and stay on top of outstanding balances — all in
                one simple workspace.
            </p>

            <div class="landing-hero-actions">

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="landing-btn landing-btn-primary">
                        Open Your Dashboard →
                    </a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="landing-btn landing-btn-primary">
                            Get Started →
                        </a>
                    @endif

                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="landing-btn landing-btn-outline">
                            I Already Have an Account
                        </a>
                    @endif
                @endauth

            </div>

        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="landing-section" id="features">
        <div class="landing-container">

            <div class="landing-section-heading">
                <h2>Everything you need to stay organized</h2>

                <p>
                    A straightforward way to manage your
                    invoicing workflow from start to payment.
                </p>
            </div>

            <div class="landing-features">

                <article class="landing-feature-card">
                    <div class="landing-feature-icon">♙</div>

                    <h3>Customer Management</h3>

                    <p>
                        Keep your customer information organized
                        in one place. Add, update, and manage
                        customers without unnecessary complexity.
                    </p>
                </article>

                <article class="landing-feature-card">
                    <div class="landing-feature-icon">▤</div>

                    <h3>Invoice Management</h3>

                    <p>
                        Create invoices with line items, calculate
                        VAT and totals, and manage the invoice
                        lifecycle from draft to paid.
                    </p>
                </article>

                <article class="landing-feature-card">
                    <div class="landing-feature-icon">€</div>

                    <h3>Payment Tracking</h3>

                    <p>
                        Record full or partial payments, monitor
                        outstanding balances, and identify
                        overdue invoices.
                    </p>
                </article>

            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="landing-cta-section">
        <div class="landing-container">

            <div class="landing-cta-box">
                <h2>Ready to simplify your invoicing?</h2>

                <p>
                    Spend less time organizing invoices
                    and more time doing what you do best.
                </p>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="landing-btn landing-btn-primary">
                        Go to Dashboard →
                    </a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="landing-btn landing-btn-primary">
                            Create Your Account →
                        </a>
                    @endif
                @endauth

            </div>

        </div>
    </section>

</main>

<footer class="landing-footer">
    <div class="landing-container landing-footer-inner">

        <span>© {{ date('Y') }} InvoiceFlow</span>

        <span>
            Built for freelancers and small businesses.
        </span>

    </div>
</footer>

</body>
</html>
