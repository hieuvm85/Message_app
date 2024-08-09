<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOTPJob;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use ResponseCustom;

class AuthController extends Controller
{
    /*
    *   tao tai khoan user
    */
    public function register(Request $request){
        try{
            $request->validate([
                "name" => "required|max:255",
                "email" => "required|email|unique:users,email",
                "password" => "required|max:255|min:5",
            ]);
            $request['remember_token'] = Str::random(10);
            $user = User::createUser($request->all());   
            $user->sendEmailVerificationNotification();
            // event(new Registered($user));
            return  response()->json($user);    
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /*
    * dang nhap
    */
    public function login(Request $request){
        try{
            $request->validate([
                "email" => "required|email",
                "password" => "required",
            ]);
            if(Auth::attempt($request->all())){
                $user=Auth::user();
                 /** @var \App\Models\User $user **/
                $token = $user->createToken('name')->accessToken;
                $dataRes =  [
                    'data' =>[
                        'token' => $token
                    ]
                    
                ];  
                return  response()->json($dataRes);    
            }
            else{
                return ResponseCustom::false('wrong password or email');
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }


    public function getOTP(Request $request){
        try{
            $request->validate([
                "email" => "required|email",
            ]);
            $email = $request->email;
            $otp = Str::random(6);

            SendOTPJob::dispatch($email,$otp);

            $cookie= Cookie::make('otp',$otp,1);

            
            return response()->json(['message' => 'check your mail'])->withCookie($cookie);
        }
        catch(Exception $e){
            return response()->json($e->getMessage());
        }
    }


    public function verifyOTP(Request $request){

        try{
            $request->validate([
                "email" => "required|email",
                "otp" => "required"
            ]);
            $email = $request->email;
            $otp = $request->otp;
            if($otp==$request->cookie("otp"))   
                return response()->json(['message' => 'verify otp success']);
            else
                return response()->json(['message' => 'wrong otp ']);
        }
        catch(Exception $e){
            return response()->json($e->getMessage());
        }
    }
}
