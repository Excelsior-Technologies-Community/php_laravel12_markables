<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = ['title', 'body'];

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function toggleMark(string $type, $user): bool
    {
        $existing = $this->marks()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        $this->marks()->create([
            'user_id' => $user->id,
            'type'    => $type,
        ]);

        return true;
    }

    public function isMarkedByUser(string $type, int $userId): bool
    {
        return $this->marks()
            ->where('type', $type)
            ->where('user_id', $userId)
            ->exists();
    }
}