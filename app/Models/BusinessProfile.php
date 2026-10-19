<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProfile extends Model
{
    protected $fillable = [
        'business_name',
        'email',
        'phone',
        'address',
        'vat_number',
        'iban',
        'bic',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
