<?php

namespace App\V1\Listeners;

use App\V1\Events\UserEmailUpdated;
use App\V1\Utils\Mail\TemplateMailable;
use App\V1\Utils\Mail\MailHelper;

class SendEmailVerificationToUser extends NowListener
{
    public function handle(UserEmailUpdated $event)
    {
        $userEmail = $event->getUserEmail();
        if ($userEmail->notVerified) {
            $user = $userEmail->user;
            MailHelper::sendNowWithTemplate(
                'user_email_verification',
                [
                    TemplateMailable::EMAIL_SUBJECT => 'Verify your email',
                    TemplateMailable::EMAIL_TO => $userEmail->email,
                    TemplateMailable::EMAIL_TO_NAME => $user->display_name,
                    'url_verify' => $event->getAppEmailVerifyUrl(),
                ],
                true,
                $user->preferredLocale()
            );
        }
    }
}
