<?php

namespace App\V1\Events;

use App\V1\Models\User;

class UserCreated extends Event
{
    /**
     * @var User
     */
    protected $user;

    protected $password;

    /**
     * UserRegistered constructor.
     * @param User $user
     * @param string $password
     */
    public function __construct($user, $password)
    {
        parent::__construct();

        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
