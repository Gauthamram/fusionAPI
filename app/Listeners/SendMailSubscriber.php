<?php

namespace App\Listeners;

use App\Notifications\NotifyUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle on user signup.
     *
     * @param  UserSignup  $event
     * @return void
     */
    public function onUserSignUp($event) 
    {
        $event->user->notify(new NotifyUser($event->user, 'signup'));
    }

    /**
     * Handle on user password reset.
     *
     * @param  UserSignup  $event
     * @return void
     */
    public function onUserResetPassword($event) 
    {
        $event->user->notify(new NotifyUser($event->user, 'reset'));
    }

     /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\UserSignUp',
            'App\Listeners\SendMailSubscriber@onUserSignUp'
        );

        $events->listen(
            'App\Events\UserPasswordReset',
            'App\Listeners\SendMailSubscriber@onUserResetPassword'
        );
    }

    /**
     * Handle the event.
     *
     * @param  UserSignup  $event
     * @return void
     */
    public function handle(UserSignup $event)
    {
               
    }
}
