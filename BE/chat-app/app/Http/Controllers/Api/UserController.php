<?php

namespace App\Http\Controllers\Api;

use App\Events\GetGroupChatEvent;
use App\Events\GetMyFriendChatEvent;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function getUser(){
        try{
            $user1 = Auth::user();

            $user = User::find($user1->id);
            // $listGroup = $user->groupsWithLastMessageAndReadStatus();
            // event(new GetGroupChatEvent($user, $listGroup));
            
            return response()->json($user);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
}
