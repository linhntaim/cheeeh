<?php

namespace App\V1\Listeners;

use App\V1\Events\UserRegistered;
use App\V1\Utils\Mail\TemplateMailable;
use App\V1\Utils\Mail\MailHelper;

class SendEmailRegistrationToUser extends NowListener
{
    public function handle(UserRegistered $event)
    {
        $user = $event->getUser();
        MailHelper::sendNowWithTemplate(
            'user_registration',
            [
                TemplateMailable::EMAIL_SUBJECT => 'Welcome you to our world',
                TemplateMailable::EMAIL_TO => $user->email->email,
                TemplateMailable::EMAIL_TO_NAME => $user->display_name,
                'user_name' => $user->name,
                'user_password' => $user->name,
                'app_name' => $event->getClientAppName(),
                'app_url' => $event->getClientAppUrl(),
            ],
            true,
            $user->preferredLocale()
        );
    }
}
