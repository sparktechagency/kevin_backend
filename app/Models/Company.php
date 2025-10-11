<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name', 'company_email', 'company_phone', 'company_address',
        'company_logo', 'manager_full_name', 'manager_email', 'manager_phone',
        'manager_code', 'send_welcome_email',
    ];
}
