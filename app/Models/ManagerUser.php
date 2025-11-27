<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerUser extends Model
{
    protected $guarded= [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function manager()
    {
        return $this->belongsTo(User::class,'manager_id','id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

}
