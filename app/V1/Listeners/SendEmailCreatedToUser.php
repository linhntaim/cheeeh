<?php

namespace App\V1\Listeners;

use App\V1\Events\UserRegistered;
use App\V1\Utils\Mail\TemplateMailable;
use App\V1\Utils\Mail\MailHelper;

class SendEmailCreatedToUser extends NowListener
{
    public function handle(UserRegistered $event)
    {
        $user = $event->getUser();
        MailHelper::sendNowWithTemplate(
            'user_created',
            [
                TemplateMailable::EMAIL_SUBJECT => static::__transListener('mail_subject', [
                    'app_name' => $event->getClientAppName(),
                ]),
                TemplateMailable::EMAIL_TO => $user->email->email,
                TemplateMailable::EMAIL_TO_NAME => $user->display_name,
                'user_name' => $user->name,
                'user_password' => $event->getPassword(),
                'app_name' => $event->getClientAppName(),
                'app_url' => $event->getClientAppUrl(),
            ],
            true,
            $user->preferredLocale()
        );
    }
}
