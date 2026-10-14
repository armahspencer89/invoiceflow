<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'vat_rate',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'vat_rate' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
    public function subtotal(): float
    {
        return round(
            $this->lines->sum(function ($line) {
                return (float) $line->quantity * (float) $line->unit_price;
            }),
            2
        );
    }

    /**
     * Calculate VAT based on the subtotal.
     */
    public function vatAmount(): float
    {
        return round(
            $this->subtotal() * ((float) $this->vat_rate / 100),
            2
        );
    }

    /**
     * Calculate the invoice total including VAT.
     */
    public function total(): float
    {
        return round(
            $this->subtotal() + $this->vatAmount(),
            2
        );
    }
}
