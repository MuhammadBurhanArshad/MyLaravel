<?php

namespace App\Services;

use App\Interfaces\MessageSender;

class SMSService implements MessageSender
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendMessage($recipient, $message) {
        echo "SMS is send to $recipient : $message";
        return true;
    }
}
