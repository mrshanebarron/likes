<?php

namespace MrShaneBarron\Likes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('sb-likes.table', 'ld_reactions'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('sb-likes.user_model', \App\Models\User::class));
    }

    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getReactionConfigAttribute(): ?array
    {
        return config("sb-likes.reactions.{$this->reaction}");
    }

    public function getEmojiAttribute(): string
    {
        return $this->reaction_config['emoji'] ?? '👍';
    }

    public function getColorAttribute(): string
    {
        return $this->reaction_config['color'] ?? '#2078f4';
    }
}
