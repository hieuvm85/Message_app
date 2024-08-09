<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Email\EmailVerificationController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Predis\Configuration\Option\Prefix;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function(){
    Route::post('register',[AuthController::class, 'register']);
    Route::post('login',[AuthController::class, 'login']);
    Route::get('email/getOTP',[AuthController::class, 'getOTP']);
    Route::get('email/verifyOTP',[AuthController::class, 'verifyOTP']);
    
});

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');


Route::get('user',[UserController::class, 'getUser'])->middleware("auth:api");  
Route::get('group',[GroupController::class, 'getListGroup'])->middleware("auth:api"); 

//// can them middleware xac thuc api
Route::get('group/{id}',[GroupController::class, 'getGroup']);  
Route::post('message',[MessageController::class, 'sendMessage']);



Route::get('test',[TestController::class, 'test']);







Route::post('pusher/auth',function(Request $request){
    $socketId = $request->socketId; // Thay thế bằng socket ID thực tế của bạn
    $userId = $socketId->userId; // Thay thế bằng user ID thực tế của bạn
    $signature = hash( 'SHA256' ,  $socketId.':'.$userId.':'.env('YOUR_APP_SECRET') );
    return [        
        'auth' => env("PUSHER_APP_KEY").":".$signature,
    ];
})->middleware("auth:api");