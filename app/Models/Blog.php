<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blog_pics()
    {
        return $this->hasMany(BlogPic::class);
    }

    public function blog_links()
    {
        return $this->hasMany(BlogLink::class);
    }
}
