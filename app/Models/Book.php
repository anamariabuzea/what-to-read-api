<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'uuid',
        'authors',
        'isbn_10',
        'isbn_13',
        'page_count',
        'category',
        'description',
        'language',
        'cover_url',
        'small_cover_url',
        'publisher',
        'published_at',
        'preview_link',
        'info_link',
        'average_rating',
        'ratings_count',
    ];
}
