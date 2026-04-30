<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    
    protected $fillable = ['title', 'body'];

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    // ✅ Toggle Mark
    public function toggleMark($type, $user)
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
            'type' => $type
        ]);

        return true;
    }

    // ✅ Check if marked
    public function isMarkedByUser($type, $userId)
    {
        return $this->marks()
            ->where('type', $type)
            ->where('user_id', $userId)
            ->exists();
    }
}