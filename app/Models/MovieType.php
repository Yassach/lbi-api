<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieType extends Model
{
    use HasFactory;

    protected $table = 'movie_type';
    protected $fillable = ['movie_id', 'type_id'];
}
