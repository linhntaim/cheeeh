<?php


namespace App\V1\Events;

use App\V1\Models\UserEmail;

class UserEmailUpdated extends Event
{
    /**
     * @var UserEmail
     */
    protected $userEmail;

    protected $appVerifyEmailPath;

    /**
     * UserEmailUpdated constructor.
     * @param UserEmail $userEmail
     * @param string $appVerifyEmailPath
     */
    public function __construct($userEmail, $appVerifyEmailPath)
    {
        parent::__construct();

        $this->userEmail = $userEmail;
        $this->appVerifyEmailPath = $appVerifyEmailPath;
    }

    /**
     * @return UserEmail
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    public function getAppEmailVerifyUrl()
    {
        return implode('/', [
            $this->clientAppUrl,
            trim($this->appVerifyEmailPath, '/'),
            $this->userEmail->email,
            $this->userEmail->verified_code,
        ]);
    }
}
