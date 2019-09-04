<?php

namespace App\Providers;

use App\V1\Events\UserCreated;
use App\V1\Events\UserEmailUpdated;
use App\V1\Events\UserRegistered;
use App\V1\Listeners\SendEmailCreatedToUser;
use App\V1\Listeners\SendEmailRegistrationToUser;
use App\V1\Listeners\SendEmailVerificationToUser;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserCreated::class => [
            SendEmailCreatedToUser::class,
        ],
        UserRegistered::class => [
            SendEmailRegistrationToUser::class,
        ],
        UserEmailUpdated::class => [
            SendEmailVerificationToUser::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
