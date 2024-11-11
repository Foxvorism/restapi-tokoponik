<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogLink extends Model
{
    use HasFactory;

    protected $fillable = ['blog_id', 'link'];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
