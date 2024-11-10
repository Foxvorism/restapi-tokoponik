<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPic extends Model
{
    use HasFactory;

    protected $fillable = ['blog_id', 'pic_path'];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
