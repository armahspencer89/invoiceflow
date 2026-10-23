<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_date',
        'due_date',
        'vat_rate',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'issued_at' => 'datetime',
            'vat_rate' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    /**
     * Calculate the invoice subtotal before VAT.
     */
    public function subtotalCents(): int
    {
        $subtotal = 0;

        foreach ($this->lines as $line) {
            $subtotal += Money::multiplyToCents(
                (string) $line->quantity,
                (string) $line->unit_price
            );
        }

        return $subtotal;
    }

    public function vatAmountCents(): int
    {
        return Money::percentageOfCents(
            $this->subtotalCents(),
            (string) $this->vat_rate
        );
    }

    public function totalCents(): int
    {
        return $this->subtotalCents()
            + $this->vatAmountCents();
    }

    public function paidCents(): int
    {
        return (int) $this->payments()->sum('amount_cents');
    }

    public function balanceCents(): int
    {
        return $this->totalCents() - $this->paidCents();
    }
    // Compatibility methods for existing Blade views

    public function subtotal(): float
    {
        return $this->subtotalCents() / 100;
    }

    public function vatAmount(): float
    {
        return $this->vatAmountCents() / 100;
    }

    public function total(): float
    {
        return $this->totalCents() / 100;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'sent'
            && $this->balanceCents() > 0
            && $this->due_date->lt(today());
    }

    public function isPartiallyPaid(): bool
    {
        return $this->status === 'sent'
            && $this->paidCents() > 0
            && $this->balanceCents() > 0;
    }

    public function displayStatus(): string
    {
        if ($this->status === 'paid') {
            return 'Paid';
        }

        if ($this->status === 'draft') {
            return 'Draft';
        }

        if ($this->isOverdue()) {
            return 'Overdue';
        }

        if ($this->isPartiallyPaid()) {
            return 'Partially Paid';
        }

        return 'Sent';
    }
}
