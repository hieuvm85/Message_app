<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    //
    private $group;
    public function __construct()
    {
        $this->group = new Group();
    }

    public function getListGroup(){
        try{
 
            $user1 = Auth::user();

            $user = User::find($user1->id);
            $listGroup = $user->groupsWithLastMessageAndReadStatus();
            $data= [
                'data' => $listGroup,
            ];

            return response()->json($data);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function getGroup(Request $request){
        try{
            $request->validate([
                'id' => 'integer',
            ]);


            $group = $this->group->getGroupById($request->id);
            $data =[
                'group' => $group,
                'messages'=>$group->messages()->paginate(100),
            ];
            return response()->json($data);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
}
