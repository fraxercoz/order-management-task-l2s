<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postcode',
        'country',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
