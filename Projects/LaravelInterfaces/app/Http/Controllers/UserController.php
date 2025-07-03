<?php

namespace App\Http\Controllers;

use App\Interfaces\MessageSender;
use App\Services\EmailService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    // public function __construct(public MessageSender $emailService) {
    //     $this->emailService = $emailService;
    // }

    public function index () {
        // $this->emailService->sendMessage("burhan@gmail.com", "Hello World");


        $emailService = app(MessageSender::class)->get('email');
        $smsService = app(MessageSender::class)->get('sms');


        $emailService->sendMessage('burhan@gmail.com', "Hello World");
        $smsService->sendMessage('0340000000', "Hello World");


        return true;
    }
}
