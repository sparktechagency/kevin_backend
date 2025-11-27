<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded =[];
    public function managerUsers()
    {
        return $this->hasMany(ManagerUser::class, 'department_id');
    }
}
