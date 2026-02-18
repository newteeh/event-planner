<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'max_participants',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Создатель события
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Участники события
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Только подтвердившие участие
    public function confirmedParticipants()
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->wherePivot('status', 'going')
                    ->withTimestamps();
    }
}