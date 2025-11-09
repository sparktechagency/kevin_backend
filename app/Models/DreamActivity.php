<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DreamActivity extends Model
{
    protected $fillable = [
        'dream_id',
        'user_id',
        'type',
        'log_checkin_in',
    ];
    public function dream()
    {
        return $this->belongsTo(Dream::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Dream Model
    public function activities()
    {
        return $this->hasMany(DreamActivity::class); 
    }

}
