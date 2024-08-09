<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    
    public static function createUser($data){
        return User::create($data);
    }

    // public static function getContacts(){
    //     $contacs = U
    // }
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'participants')
                    ->withPivot('last_read');
    }

    public function messageStatuses()
    {
        return $this->hasMany(MessageStatus::class);
    }


    public function groupsWithLastMessageAndReadStatus()
    {
        // $groups = DB::table('participants')
        // ->where('user_id', $this->id)
        // ->pluck('group_id'); // Lấy tất cả ID nhóm mà người dùng tham gia

        // return Group::whereIn('id', $groups)
        //             ->with(['messages' => function($query) {
        //                 $query->latest()->first();
        //             }])
        //             ->get()
        //             ->map(function($group) {
        //                 $group->last_message = $group->messages->first();
        //                 unset($group->messages); // Remove the messages collection to avoid redundancy
        //                 return $group;
        //             })
        //             ->map(function($group) {
        //                 $lastRead = $group->pivot->last_read;
        //                 $group->last_read = $lastRead;
        //                 $group->unread_messages = $group->last_message ? $group->last_message->created_at > $lastRead : false;
        //                 return $group;
        //             });
        $groupIds = DB::table('participants')
        ->where('user_id', $this->id)
        ->pluck('group_id');

    // Truy vấn các nhóm cùng với tin nhắn
    return Group::whereIn('id', $groupIds)
                ->with(['users' => function($query) {
                    $query->select('users.id', 'users.name', 'users.email'); // Adjust fields as necessary
                }])
                ->with(['messages' => function($query) {
                    $query->orderBy('created_at', 'desc'); // Sắp xếp tin nhắn theo thứ tự giảm dần của created_at
                }])
                ->get()
                ->map(function($group) {
                    // Lấy tin nhắn mới nhất cho nhóm
                    $lastMessage = $group->messages->first();
                    unset($group->messages); // Xóa danh sách tin nhắn để tránh trùng lặp
                    $group->last_message = $lastMessage;
                    return $group;
                })
                ->map(function($group) {
                    // Lấy giá trị last_read từ bảng participants
                    $lastRead = DB::table('participants')
                        ->where('user_id', $this->id)
                        ->where('group_id', $group->id)
                        ->value('last_read');
                        
                    $group->last_read = $lastRead;
                    $group->unread_messages = $group->last_message ? $group->last_message->created_at > $lastRead : false;
                    return $group;
                });
    }
}
