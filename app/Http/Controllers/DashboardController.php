<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $invoices = Invoice::query()
            ->whereHas('customer', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['lines', 'payments'])
            ->get();

        $totalInvoices = $invoices->count();

        $outstandingCents = $invoices
            ->filter(fn ($invoice) => $invoice->status !== 'draft')
            ->sum(fn ($invoice) => $invoice->balanceCents());

        $overdueInvoices = $invoices
            ->filter(fn ($invoice) => $invoice->isOverdue())
            ->count();

        $paymentsReceivedCents = Payment::query()
            ->whereHas('invoice.customer', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount_cents');

        $recentInvoices = $invoices
            ->sortByDesc('created_at')
            ->take(5);

        return view('dashboard', compact(
            'totalInvoices',
            'outstandingCents',
            'overdueInvoices',
            'paymentsReceivedCents',
            'recentInvoices'
        ));
    }
}
