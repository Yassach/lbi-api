<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoviePeople extends Model
{
    use HasFactory;

    protected $table = 'movie_people';
    protected $fillable = ['movie_id', 'people_id', 'role', 'significance'];
}
