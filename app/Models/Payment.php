<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_number',
        'price',
        'status',
        'payment_method',
        'qr_url',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


