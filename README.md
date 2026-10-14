# InvoiceFlow

**A SaaS-style invoice and payment management application built with Laravel.**

InvoiceFlow is an independent full-stack portfolio project designed to help freelancers and small businesses manage customers, create invoices, track payments, and monitor outstanding balances through a simple, responsive interface.

The project demonstrates practical Laravel development, relational database design, authentication, authorization, financial calculations, automated testing, and modern UI/UX practices.

> **Project status:** In active development — core customer, invoicing, and payment workflows implemented. PDF generation and email delivery are planned.

## Features

### Authentication and Security
- User registration, login, and logout
- Password reset functionality using Laravel Fortify
- User-specific customer and invoice records
- Laravel policies for authorization
- Server-side request validation
- CSRF protection

### Customer Management
- Create, view, edit, and manage customers
- Store customer contact and business information
- Activate and deactivate customer records
- Restrict customer access to the authenticated owner

### Invoice Management
- Create invoices with multiple line items
- Assign invoices to customers
- Set invoice dates, due dates, and VAT rates
- Automatically calculate subtotals, VAT, and invoice totals
- Edit and delete draft invoices
- Mark draft invoices as sent
- Prevent editing after an invoice has been sent
- Search and filter invoices by status

### Payment Management
- Record full and partial payments
- Support bank transfer, cash, card, and other payment methods
- Store payment dates, references, and notes
- Automatically update outstanding balances
- Prevent payments exceeding the remaining balance
- Automatically mark fully paid invoices as paid
- Display payment history
- Identify overdue invoices

### Dashboard and User Interface
- Financial dashboard with invoice statistics
- Recent invoice overview
- Responsive desktop and mobile layouts
- Sidebar navigation and mobile menu
- Status badges and financial summaries
- Dynamic invoice line items
- Live estimated invoice totals
- Validation feedback and confirmation dialogs

## Technology Stack

| Category | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Laravel Blade, HTML, CSS, JavaScript |
| Styling | Tailwind CSS v4, custom modular CSS |
| Database | MySQL |
| Authentication | Laravel Fortify |
| ORM | Laravel Eloquent |
| Asset Bundling | Vite |
| Dependency Management | Composer, npm |
| Testing | Laravel's PHP testing tools |
| Version Control | Git and GitHub |

## Application Workflow

InvoiceFlow follows a straightforward invoice-to-payment workflow:

1. **Register or log in** to access a private workspace.
2. **Create customers** and maintain their information.
3. **Create an invoice** with one or more products or services.
4. **Review the invoice** and its calculated totals.
5. **Mark the invoice as sent** when it is ready to issue.
6. **Record payments** as the customer pays.
7. **Track the balance** until the invoice is fully paid.
8. **Monitor overdue invoices** and payment history.

Invoice statuses include Draft, Sent, Partially Paid, Paid, and Overdue. Partial and overdue states are calculated from invoice and payment information.

## Installation and Setup

### Prerequisites

Ensure the following are installed:

- PHP 8.2 or newer
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

### 6. Run database migrations

```bash
php artisan migrate
```

### 7. Start the application

Start the Laravel development server:

```bash
php artisan serve
```

In another terminal, start Vite:

```bash
npm run dev
```

Visit:

`http://127.0.0.1:8000`

Register an account to begin using InvoiceFlow.

## Running Tests

Run the automated test suite:

```bash
php artisan test
```

Build the frontend assets:

```bash
npm run build
```

The project includes feature tests covering payment management, authorization, and invoice-related business rules.

## Project Architecture

InvoiceFlow follows Laravel's MVC architecture.

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
└── Feature/
```

### Key Design Decisions

**Ownership-based authorization:** Customer and invoice records are restricted to their authenticated owners using Laravel policies.

**Accurate monetary calculations:** Invoice and payment calculations use integer cents and a dedicated money utility to reduce floating-point errors.

**Transactional payment recording:** Payment processing uses database transactions and row locking to protect against concurrent overpayments.

**Separation of concerns:** Controllers, models, validation requests, policies, and views have distinct responsibilities.

**Modular styling:** CSS is organized into reusable components, application layouts, and page-specific stylesheets.

## Development Roadmap

| Milestone | Status |
|---|---|
| Laravel project setup and database configuration | Complete |
| Authentication and user accounts | Complete |
| Customer management | Complete |
| Invoice creation and management | Complete |
| Invoice calculations and status management | Complete |
| Payment recording and outstanding balances | Complete |
| Financial dashboard | Complete |
| Responsive UI/UX redesign | Complete |
| Professional PDF invoice generation | Planned |
| Email invoice delivery | Planned |
| Further testing and production readiness | Planned |
| Deployment | Planned |

## Screenshots

Application screenshots will be added to showcase the dashboard, customer management, invoice creation, and payment tracking interfaces.

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

Planned enhancements include professional PDF invoices, email delivery, improved automated test coverage, and deployment.

The longer-term goal is to explore InvoiceFlow's potential as a practical invoicing product for freelancers and small businesses.

## Author

**Spencer Armah**

Application Development student at Thomas More University of Applied Sciences, Belgium.

GitHub: [@armahspencer89](https://github.com/armahspencer89)

## License

No open-source license has been specified for this project.
