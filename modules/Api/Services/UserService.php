<?php

namespace Modules\Api\Services;

use App\Models\User;
use Illuminate\Http\Response;
use Modules\Api\Mail\ReportMail;

class UserService
{
    public function sendReport(User $reporter, User $reportable, string $reason): void
    {
        abort_if($reportable->id === $reporter->id, Response::HTTP_FORBIDDEN, 'You cannot report about self.');

        \Mail::to(config('tattoo.mail_system_receivers'))->send(
            (new ReportMail($reporter, $reportable, $reason))->to(config('tattoo.mail_system_receivers'))
        );
    }

    public function toggleBlacklist(User $blocker, User $blockable): bool
    {
        abort_if($blockable->id === $blocker->id, Response::HTTP_FORBIDDEN, 'You cannot add self to blacklist.');

        $changes = $blocker->blacklist()->toggle([$blockable->id => ['blocked_at' => \DB::raw('NOW()')]]);

        if ($blocked = !empty($changes['attached'])) {
            $blocker->subscribers()->detach($blockable->id);
            $blocker->subscriptions()->detach($blockable->id);
        }

        return $blocked;
    }

    public function toggleSubscription(User $subscriber, User $subscribable): bool
    {
        abort_if($subscribable->id === $subscriber->id, Response::HTTP_FORBIDDEN, 'You cannot subscribe to self.');

        abort_if(
            $subscriber->blacklist()->where('blocked_id', '=', $subscribable->id)->exists(),
            Response::HTTP_FORBIDDEN,
            'You cannot subscribe to blocked user.'
        );

        $changes = $subscriber->subscriptions()->toggle([
            $subscribable->id => ['subscribed_at' => \DB::raw('NOW()')]
        ]);

        return !empty($changes['attached']);
    }
}
