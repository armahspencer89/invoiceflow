<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $invoices = Invoice::query()
            ->where('user_id', $userId)
            ->whereHas('customer', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['lines', 'payments'])
            ->lazy(100);

        $totalInvoices = Invoice::query()
            ->where('user_id', $userId)
            ->whereHas('customer', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        $outstandingCents = 0;
        $overdueInvoices = 0;

        foreach ($invoices as $invoice) {
            // Draft invoices do not contribute to outstanding balances.
            if ($invoice->status !== 'draft') {
                $outstandingCents += $invoice->balanceCents();
            }

            // Count overdue invoices, including partially paid ones.
            if ($invoice->isOverdue()) {
                $overdueInvoices++;
            }
        }

        $paymentsReceivedCents = Payment::query()
            ->whereHas('invoice', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereHas('customer', function ($customerQuery) use ($userId) {
                        $customerQuery->where('user_id', $userId);
                    });
            })
            ->sum('amount_cents');

        $recentInvoices = Invoice::query()
            ->where('user_id', $userId)
            ->whereHas('customer', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['customer', 'lines', 'payments'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalInvoices',
            'outstandingCents',
            'overdueInvoices',
            'paymentsReceivedCents',
            'recentInvoices'
        ));
    }
}
