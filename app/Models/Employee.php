<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';
    protected $fillable = [
        'firstname',
        'lastname',
        'image',
        'date_of_birth',
        'user_id',
        'warehouse_id',
        'phone',
        'salary',
        'city',
        'address'
    ];
}
