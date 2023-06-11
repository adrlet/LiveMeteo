<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\URL;

class ResetPassword extends ResetPasswordNotification
{
    protected function resetUrl($notifiable)
    {
        $urlLen = strlen(url(""));
        $url = substr(url(""), 0, $urlLen-4).'3000/reset-password/'.$this->token.'?email='.$notifiable->email;

        return $url;
    }
}
