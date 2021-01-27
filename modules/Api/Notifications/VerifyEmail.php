<?php

namespace Modules\Api\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $code = $notifiable->email_verification_code;

        return (new MailMessage)
            ->subject(__('Verify Email Address'))
            ->line(__("Please enter this code in app to verify your email address: **:code**.", [
                'code' => $code
            ]))
            ->line(__('If you did not create an account, no further action is required.'));
    }
}
