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

    public function marksByType($type)
    {
        return $this->marks()->where('type', $type);
    }

    public function mark($type, $user)
    {
        return $this->marks()->updateOrCreate(
            ['user_id' => $user->id, 'type' => $type],
            ['post_id' => $this->id]
        );
    }
}