<?php

namespace App\Models;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{  protected $fillable=['title','body','user_id'];
public function user()
{
    return $this->belongsTo(User::class);
}
public function comments()
{
    return $this->hasMany(Comment::class);
}
}
