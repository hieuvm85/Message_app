<?php

namespace App\Http\Controllers;

use App\Events\GetGroupChatEvent;
use App\Events\SendMessageEvent;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    //
    public function sendMessage(Request $request){
        try{
            $request->validate([
                // validation
            ]);
            $group = Group::find($request->group_id);
            $user = User::find($request->user_id);
            $message =  new Message();
            $message->sender_id = $request->sender_id;
            $message->group_id = $request->group_id;
            $message->content = $request->content;
            
            $message->save();
    
            event(new SendMessageEvent($group,$message));   

            foreach($group->users as $user){
                event(new GetGroupChatEvent($user));
            }
           
            return response()->json(['status' => 200, 'Message' => $group->users]);
        }
        catch(Exception $e){
            return $e->getMessage();
        }

    }
}
