
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>InvoiceFlow — Simple Invoicing for Small Businesses</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .landing-page {
            font-family: 'Instrument Sans', sans-serif;
            background: #ffffff;
            color: #0f172a;
        }

        .landing-container {
            width: min(1120px, 92%);
            margin: 0 auto;
        }

        .landing-nav {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        .landing-nav-inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .landing-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.7px;
            color: #0f172a;
        }

        .landing-logo-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 800;
        }

        .landing-nav-actions,
        .landing-hero-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .landing-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .landing-btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .landing-btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .landing-btn-outline {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }

        .landing-btn-outline:hover {
            background: #f8fafc;
        }

        .landing-hero {
            padding: 100px 0;
            background: linear-gradient(145deg, #f8fafc, #eff6ff);
        }

        .landing-hero-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .landing-eyebrow {
            display: inline-block;
            padding: 8px 15px;
            margin-bottom: 25px;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
        }

        .landing-hero h1 {
            font-size: clamp(38px, 6vw, 64px);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -2px;
            margin: 0 0 24px;
        }

        .landing-hero h1 span {
            color: #2563eb;
        }

        .landing-hero-description {
            max-width: 620px;
            margin: 0 auto 32px;
            font-size: 18px;
            line-height: 1.8;
            color: #64748b;
        }

        .landing-hero-actions {
            justify-content: center;
        }

        .landing-section {
            padding: 85px 0;
        }

        .landing-section-heading {
            text-align: center;
            margin-bottom: 45px;
        }

        .landing-section-heading h2 {
            font-size: clamp(28px, 4vw, 38px);
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 14px;
        }

        .landing-section-heading p {
            color: #64748b;
            font-size: 16px;
            line-height: 1.7;
        }

        .landing-features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .landing-feature-card {
            padding: 30px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.2s ease;
        }

        .landing-feature-card:hover {
            border-color: #bfdbfe;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .landing-feature-icon {
            display: grid;
            place-items: center;
            width: 50px;
            height: 50px;
            margin-bottom: 22px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 12px;
            font-size: 24px;
            font-weight: 700;
        }

        .landing-feature-card h3 {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .landing-feature-card p {
            font-size: 14px;
            line-height: 1.75;
            color: #64748b;
        }

        .landing-cta-section {
            padding: 65px 0 85px;
        }

        .landing-cta-box {
            padding: 65px 25px;
            background: #0f172a;
            border-radius: 22px;
            text-align: center;
            color: #ffffff;
        }

        .landing-cta-box h2 {
            font-size: clamp(27px, 4vw, 36px);
            font-weight: 800;
            margin-bottom: 16px;
        }

        .landing-cta-box p {
            color: #cbd5e1;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .landing-footer {
            padding: 28px 0;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 13px;
        }

        .landing-footer-inner {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .landing-hero {
                padding: 70px 0;
            }

            .landing-features {
                grid-template-columns: 1fr;
            }

            .landing-section {
                padding: 60px 0;
            }

            .landing-logo {
                font-size: 19px;
            }

            .landing-nav-actions {
                gap: 8px;
            }

            .landing-nav-actions .landing-btn {
                padding: 10px 12px;
                font-size: 12px;
            }

            .landing-cta-box {
                padding: 45px 20px;
            }
        }
    </style>
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
