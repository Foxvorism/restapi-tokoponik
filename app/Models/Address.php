<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'address', 'note', 'province', 'district', 'subdistrict', 'post_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
