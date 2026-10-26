<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model 
{
    use HasFactory;

    protected $fillable = array('title', 'content', 'status', 'user_id');
}
