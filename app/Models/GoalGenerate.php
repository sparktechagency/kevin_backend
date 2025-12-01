<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalGenerate extends Model
{
    protected $guarded = [];
    public function employee() {
    return $this->belongsTo(User::class, 'employee_id');
    }

    public function mentor() {
        return $this->belongsTo(User::class, 'mentor_id');
    }

}
