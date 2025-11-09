<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded =[];
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'comment_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
