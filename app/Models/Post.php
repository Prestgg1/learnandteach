<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasFactory, HasTags;
    protected $fillable = [
        "title",
        "slug",
        "content",
        "description",
        "tags",
        "author",
        "active",
        "thumbnail",
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom("title")
            ->saveSlugsTo("slug");
    }

    protected static function booted()
    {
        static::addGlobalScope("withUser", function ($builder) {
            $builder->with("user");
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, "author");
    }

    public function getAuthorNameAttribute()
    {
        return $this->user->name ?? null;
    }

    public function getAuthorEmailAttribute()
    {
        return $this->user->email ?? null;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
