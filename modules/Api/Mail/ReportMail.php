<?php

namespace Modules\Api\Mail;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;

class ReportMail extends Mailable implements ShouldQueue
{
    public $subject = 'Новая жалоба';

    private MailMessage $message;

    public function __construct(User $reporter, User $user, string $reason)
    {
        $this->message = (new MailMessage)->salutation(null);

        $this->message
            ->line("Пользователь $reporter->nickname пожаловался на пользователя $user->nickname по причине:")
            ->line($reason);
    }

    public function build(): self
    {
        return $this->markdown('vendor.notifications.email', $this->message->data());
    }
}
