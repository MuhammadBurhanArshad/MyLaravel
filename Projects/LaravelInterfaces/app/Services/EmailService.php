<?php

namespace App\Services;

use App\Interfaces\MessageSender;

class EmailService implements MessageSender
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendMessage($recipient, $message) {
        echo "Email is send to $recipient : $message";
        return true;
    }
}
