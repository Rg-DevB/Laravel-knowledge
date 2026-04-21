<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SolutionVotedNotification extends Notification
{
    public function __construct(
        public readonly \App\Models\Solution $solution,
        public readonly int $value
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $action = $this->value > 0 ? 'upvoted' : 'downvoted';
        return [
            'message' => "Your solution on \"{$this->solution->problem->title}\" was {$action}.",
            'url'     => route('problems.show', $this->solution->problem->slug),
            'type'    => 'vote',
        ];
    }
}
