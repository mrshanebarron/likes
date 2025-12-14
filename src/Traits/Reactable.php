<?php

namespace MrShaneBarron\Likes\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use MrShaneBarron\Likes\Models\Reaction;

trait Reactable
{
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function react(string $reaction, ?int $userId = null, ?string $sessionId = null): Reaction
    {
        $userId = $userId ?? Auth::id();
        $sessionId = $sessionId ?? session()->getId();

        // Remove existing reaction first
        $this->unreact($userId, $sessionId);

        return $this->reactions()->create([
            'user_id' => $userId,
            'reaction' => $reaction,
            'session_id' => $userId ? null : $sessionId,
        ]);
    }

    public function unreact(?int $userId = null, ?string $sessionId = null): bool
    {
        $userId = $userId ?? Auth::id();
        $sessionId = $sessionId ?? session()->getId();

        $query = $this->reactions();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->delete() > 0;
    }

    public function toggleReaction(string $reaction, ?int $userId = null, ?string $sessionId = null): ?Reaction
    {
        $userId = $userId ?? Auth::id();
        $sessionId = $sessionId ?? session()->getId();

        $existing = $this->getUserReaction($userId, $sessionId);

        if ($existing && $existing->reaction === $reaction) {
            $this->unreact($userId, $sessionId);
            return null;
        }

        return $this->react($reaction, $userId, $sessionId);
    }

    public function getUserReaction(?int $userId = null, ?string $sessionId = null): ?Reaction
    {
        $userId = $userId ?? Auth::id();
        $sessionId = $sessionId ?? session()->getId();

        $query = $this->reactions();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->first();
    }

    public function hasReacted(?int $userId = null, ?string $sessionId = null): bool
    {
        return $this->getUserReaction($userId, $sessionId) !== null;
    }

    public function getReactionCounts(): Collection
    {
        return $this->reactions()
            ->selectRaw('reaction, COUNT(*) as count')
            ->groupBy('reaction')
            ->pluck('count', 'reaction');
    }

    public function getTotalReactions(): int
    {
        return $this->reactions()->count();
    }

    public function getTopReactions(int $limit = 3): Collection
    {
        return $this->reactions()
            ->selectRaw('reaction, COUNT(*) as count')
            ->groupBy('reaction')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }
}
