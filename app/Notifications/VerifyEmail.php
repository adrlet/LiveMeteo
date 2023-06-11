<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailNotification
{

    protected function verificationUrl($notifiable)
    {
        $urlLen = strlen(url(""));
        $url = substr(url(""), 0, $urlLen-4).'3000';
        $id =  $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());

        $tempUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $id,
                'hash' => $hash,
            ]
        );

        return $url.substr($tempUrl, $urlLen+4);
    }
}
