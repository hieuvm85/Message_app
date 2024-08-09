<?php

namespace App\Http\Controllers\Api;

use App\Events\SendMessageEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Mailer\Event\SentMessageEvent;

class TestController extends Controller
{
    //
    // public function test(){
    //     event(new SendMessageEvent('test ne'));
    //     return response("ok");
    // }
}
