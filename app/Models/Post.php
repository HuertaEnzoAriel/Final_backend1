<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use HasFactory<PostFactory>
 */
class Post extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;
    public const STATUS_REJECTED = 2;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_path',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function isPublished(): bool
    {
        return (int) $this->is_published === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return (int) $this->is_published === self::STATUS_DRAFT;
    }

    public function isRejected(): bool
    {
        return (int) $this->is_published === self::STATUS_REJECTED;
    }

    public function publicationStatusLabel(): string
    {
        return match ((int) $this->is_published) {
            self::STATUS_PUBLISHED => 'Publicado',
            self::STATUS_REJECTED => 'Rechazado',
            default => 'Sin publicar',
        };
    }

    public function publicationStatusClass(): string
    {
        return match ((int) $this->is_published) {
            self::STATUS_PUBLISHED => 'ok',
            self::STATUS_REJECTED => 'rejected',
            default => 'draft',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
