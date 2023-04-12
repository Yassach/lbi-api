<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'duration', 'url'];

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'movie_type')->select(['id', 'name']);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(People::class, 'movie_people')
            ->select(['id', 'firstname', 'lastname', 'date_of_birth', 'nationality']);
    }
}
