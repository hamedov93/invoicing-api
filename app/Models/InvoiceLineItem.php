<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLineItem extends Model
{
    use HasFactory;

    // We could manage this in a database table for management by an Admin
    public const LineItemConfig = [
        'registration' => [
            'price' => 50,
        ],
        'activation' => [
            'price' => 100,
        ],
        'appointment' => [
            'price' => 200,
        ],
    ];

    protected $fillable = [
        'invoice_id',
        'name',
        'quantity',
        'price',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
