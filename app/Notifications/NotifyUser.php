<?php

namespace App\Notifications;

use App\User;
use Config;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        switch ($this->type) {
            case 'signup':
                return (new MailMessage)
                    ->subject('Registration - '.Config::get('app.name'))
                    ->greeting('Hello '.ucfirst($this->user->name))
                    ->line('Your registration has been successful. You can now sign in with the credentials using the link below:')
                    ->action('Login', url(Config::get('boilerplate.login_link')))
                    ->line('If you have any further queries, Please contact Support desk @ '.Config::get('app.support_email'). ' or Call '.Config::get('app.support_phone'));
                break;
            case 'reset':
                return (new MailMessage)
                    ->subject('Password has been reset - '.Config::get('app.name'))
                    ->greeting('Hello '.ucfirst($this->user->name))
                    ->line('Your password has been successfully reset. You can now sign in with new credentials using the link below:')
                    ->action('Login', url(Config::get('boilerplate.login_link')))
                    ->line('If you have any further queries, Please contact Support desk @ '.Config::get('app.support_email'). ' or Call '.Config::get('app.support_phone'));
                break;
            default:
                return true;
                break;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
