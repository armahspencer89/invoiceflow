# InvoiceFlow

**A SaaS-style invoice and payment management application built with Laravel 12.**

InvoiceFlow is an independent full-stack portfolio project designed to help freelancers and small businesses manage customers, create professional invoices, send invoices by email, record payments, and monitor outstanding balances through a responsive interface.

The project demonstrates practical Laravel development, relational database design, authentication, authorization, financial calculations, PDF generation, email integration, automated testing, and modern UI/UX practices.

> **Project status:** Core MVP completed and locally validated. Production deployment is planned.

## Features

### Authentication and Security

- User registration, login, and logout using Laravel Fortify
- User-specific customers, invoices, payments, and business profiles
- Ownership-based authorization using Laravel policies and database query restrictions
- Server-side request validation and CSRF protection
- Protection against cross-user access and unauthorized modifications
- Automated security regression tests

### Customer Management

- Create, view, edit, and manage customers
- Store customer contact and business information
- Activate and deactivate customer records
- Search customers by name, company, or email
- Restrict customer access to the authenticated owner

### Invoice Management

- Create invoices with multiple line items
- Assign invoices to customers
- Set invoice dates, due dates, and VAT rates
- Automatically calculate subtotals, VAT, and grand totals
- Edit and delete draft invoices
- Issue invoices with sequential numbering in the `INV-YYYY-0001` format
- Maintain independent invoice number sequences for each user and year
- Prevent backdated invoice issuance
- Prevent unauthorized changes to invoice ownership and protected fields
- Lock invoice editing after issuance
- Search and filter invoices by status

### PDF Generation and Email Delivery

- Generate downloadable PDF invoices using DomPDF
- Include business profile information and customer details
- Display invoice numbers, dates, line items, VAT, and totals
- Send issued invoices to customers using SMTP
- Attach generated PDF invoices to outgoing emails
- Prevent unauthorized PDF downloads and email sending
- Require a configured business profile for invoice email delivery

### Business Profile Management

- Configure business name and contact information
- Store business address, VAT number, IBAN, and BIC
- Update business details through authenticated settings
- Include business information in generated invoices
- Maintain separate business profiles for individual users

### Payment Management

- Record full and partial payments
- Support multiple payment methods
- Store payment dates, references, and notes
- Automatically calculate outstanding balances
- Prevent zero, negative, excessive, and invalid payment amounts
- Prevent payments against draft or fully paid invoices
- Automatically mark fully paid invoices as paid
- Display payment history
- Identify overdue and partially paid invoices

### Financial Dashboard

- Display total invoice count
- Calculate outstanding balances
- Display total payments received
- Count overdue invoices
- Show the five most recent invoices
- Display invoice statuses using visual badges
- Enforce user ownership when calculating financial statistics
- Process invoice records in batches to reduce memory usage

### User Interface

- Responsive desktop and mobile layouts
- Sidebar navigation and mobile menu
- Status badges and financial summaries
- Dynamic invoice line items
- Live estimated invoice totals
- Validation feedback and confirmation dialogs
- Modular CSS architecture

## Technology Stack

| Category | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Laravel Blade, HTML, CSS, JavaScript |
| Styling | Tailwind CSS v4, custom modular CSS |
| Database | MySQL |
| Authentication | Laravel Fortify |
| ORM | Laravel Eloquent |
| PDF Generation | DomPDF |
| Email Delivery | SMTP (tested with Brevo) |
| Asset Bundling | Vite |
| Dependency Management | Composer, npm |
| Testing | PHPUnit, Laravel feature and unit tests |
| Version Control | Git and GitHub |

## Application Workflow

InvoiceFlow follows a complete invoice-to-payment workflow:

1. **Register or log in** to access a private workspace.
2. **Configure a business profile** with company and payment details.
3. **Create customers** and maintain their information.
4. **Create an invoice** with one or more products or services.
5. **Review the invoice** and its calculated totals.
6. **Issue the invoice** to assign a sequential invoice number.
7. **Generate and download the PDF invoice.**
8. **Send the invoice by email** with the PDF attached.
9. **Record partial or full payments** as the customer pays.
10. **Track outstanding balances** until the invoice is fully paid.
11. **Monitor overdue invoices** and payment history through the dashboard.

Invoice statuses include Draft, Sent, Partially Paid, Paid, and Overdue. Partial and overdue states are calculated from invoice and payment information.

## Installation and Setup

### Prerequisites

- PHP 8.2 or newer with the required Laravel extensions, including BCMath
- Composer
- Node.js and npm
- MySQL
- Git

### 1. Clone the repository

```bash
git clone https://github.com/armahspencer89/invoiceflow.git
cd invoiceflow
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Configure environment variables

Copy the example environment file:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Set the application name in `.env`:

```dotenv
APP_NAME=InvoiceFlow
```

### 5. Configure MySQL

Create a MySQL database named `invoiceflow`.

Update the database settings in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoiceflow
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the database username and password for your local environment.

### 6. Configure email delivery (optional)

To send real invoices by email, configure an SMTP provider in `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-verified-sender@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Use the settings provided by your SMTP service. The example above is illustrative; exact settings may differ by provider.

For local testing without sending real emails, Laravel's log mailer can be used:

```dotenv
MAIL_MAILER=log
```

Never commit SMTP credentials or other secrets to Git.

### 7. Run database migrations

```bash
php artisan migrate
```

### 8. Start the application

Start the Laravel development server:

```bash
php artisan serve
```

In another terminal, start Vite:

```bash
npm run dev
```

Visit:

http://127.0.0.1:8000

Register an account to begin using InvoiceFlow.

## Running Tests

Run the complete automated test suite:

```bash
php artisan test
```

**Latest verified result: 86 passing tests and 287 assertions.**

The test suite covers:

- Business profile management and ownership
- Customer management and authorization
- Invoice numbering and issuance safeguards
- Invoice ownership and protected fields
- PDF generation and access control
- Invoice email delivery
- Partial and full payment workflows
- Overpayment prevention and payment validation
- Dashboard financial metrics and security
- Monetary rounding and integer overflow protection

The project uses a separate in-memory SQLite database for automated tests.

To build frontend assets:

```bash
npm run build
```

## Project Architecture

InvoiceFlow follows Laravel's Model-View-Controller (MVC) architecture.

```text
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
├── Policies/
└── Support/
    └── Money.php

database/
└── migrations/

resources/
├── css/
│   ├── components/
│   ├── layouts/
│   └── pages/
├── js/
└── views/
    ├── auth/
    ├── customers/
    ├── invoices/
    └── layouts/

routes/
└── web.php

tests/
├── Feature/
└── Unit/
```

### Key Design Decisions

**Ownership-based authorization:** Customer and invoice records are restricted to authenticated owners through policies, query restrictions, and server-side validation.

**Accurate monetary calculations:** Invoice and payment calculations use integer cents and a dedicated `Money` utility to reduce floating-point rounding errors.

**Sequential invoice numbering:** Issued invoices receive user-specific, year-based sequential numbers. Draft invoices do not consume invoice numbers.

**Transactional payment recording:** Payment processing uses database transactions and row locking to help prevent concurrent overpayments.

**Separation of concerns:** Controllers, models, validation requests, policies, and views have distinct responsibilities.

**PDF and email integration:** Invoice PDFs are generated through DomPDF and can be delivered as email attachments using SMTP.

**Modular styling:** CSS is organized into reusable components, application layouts, and page-specific stylesheets.

**Automated regression testing:** Feature and unit tests verify critical business rules, authorization, monetary calculations, and invoice workflows.

## Development Roadmap

| Milestone | Status |
|---|---|
| Laravel project setup and database configuration | Complete |
| Authentication and user accounts | Complete |
| Customer management | Complete |
| Invoice creation and management | Complete |
| Invoice calculations and status management | Complete |
| Payment recording and outstanding balances | Complete |
| Business profile management | Complete |
| Professional PDF invoice generation | Complete |
| Email invoice delivery | Complete |
| Financial dashboard | Complete |
| Responsive UI/UX redesign | Complete |
| Sequential invoice numbering and issuance safeguards | Complete |
| Automated regression testing | Complete |
| Local end-to-end MVP validation | Complete |
| Production deployment | Planned |

## Screenshots

Application screenshots will be added to showcase the dashboard, customer management, invoice creation, PDF generation, and payment tracking interfaces.

## Project Purpose

InvoiceFlow was developed as an independent portfolio project to strengthen practical software engineering skills and explore the development of SaaS applications.

Its goals include:

- Building a real-world application beyond classroom assignments
- Applying Laravel backend and database concepts
- Implementing secure multi-user data access
- Developing reliable financial business logic
- Practicing automated testing and Git workflows
- Designing a responsive, user-friendly application
- Exploring the process of turning a software idea into a usable product

## Future Improvements

Potential future enhancements include:

- Production deployment and hosting
- Automated CI/CD pipelines
- Additional reporting and financial analytics
- Recurring invoices
- Automated payment reminders
- Customer self-service invoice access
- Additional end-to-end browser testing
- Further performance and security hardening

The longer-term goal is to explore InvoiceFlow's potential as a practical invoicing product for freelancers and small businesses.

## Author

**Spencer Armah**

Application Development student at Thomas More University of Applied Sciences, Belgium.

GitHub: [@armahspencer89](https://github.com/armahspencer89)

## License

No open-source license has been specified for this project.
