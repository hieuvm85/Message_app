<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'sender_id', 'content'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function messageStatuses()
    {
        return $this->hasMany(MessageStatus::class);
    }
}
