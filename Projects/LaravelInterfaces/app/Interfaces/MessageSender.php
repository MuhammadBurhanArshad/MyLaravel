<?php

namespace App\Interfaces;

interface MessageSender
{
    public function sendMessage($recipient, $message);
}
