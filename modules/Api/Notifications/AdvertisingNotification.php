<?php

namespace Modules\Api\Notifications;

use App\Models\Advertising\Top;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Api\Http\Resources\Notifications\AdvertisingNotification as AdvertisingResource;

class AdvertisingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Top $top;

    protected string $resourceClass = AdvertisingResource::class;

    public function __construct(Top $top)
    {
        $this->top = $top;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)->line(__('tattoo.notifications.advertising_expires', [
            'date' => $this->top->end_at->format('d.m.Y')
        ]));
    }

    public function toDatabase(User $notifiable): array
    {
        return array_merge($this->toArray($notifiable), ['resourceClass' => $this->resourceClass]);
    }

    public function toArray(User $notifiable): array
    {
        return ['top' => $this->top->withoutRelations()];
    }
}
