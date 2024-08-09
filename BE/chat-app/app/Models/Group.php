<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'participants')
                    ->withPivot('last_read');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'group_id');
    }



    //----------------------------------------------------------------
    public function getGroupById($id){
        $group = Group::with('users')->findOrFail($id);
        return $group;
    }

}
