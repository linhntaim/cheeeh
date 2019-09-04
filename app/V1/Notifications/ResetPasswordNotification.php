<?php

namespace App\V1\Notifications;

class ResetPasswordNotification extends NowNotification
{
    protected $token;
    protected $appResetPasswordPath;

    public function __construct($token, $fromUser = null)
    {
        parent::__construct($fromUser);

        $this->token = $token;

        $this->appResetPasswordPath = request()->input('app_reset_password_path');

        $this->shouldMail();
    }

    protected function getMailTemplate($notifiable)
    {
        return 'user_password_reset';
    }

    protected function getMailSubject($notifiable)
    {
        return 'Reset password';
    }

    protected function getMailParams($notifiable)
    {
        return [
            'url_reset_password' => $this->getAppResetPasswordUrl($notifiable),
        ];
    }

    public function getAppResetPasswordUrl($notifiable)
    {
        return $this->clientAppUrl . '/' . $this->appResetPasswordPath . '/' . $notifiable->preferredEmail() . '/' . $this->token;
    }
}
