<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewSolutionNotification extends Notification
{
    public function __construct(
        public readonly \App\Models\Solution $solution
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "{$this->solution->user->name} posted a solution to your problem: \"{$this->solution->problem->title}\"",
            'url'     => route('problems.show', $this->solution->problem->slug),
            'type'    => 'new_solution',
        ];
    }
}
