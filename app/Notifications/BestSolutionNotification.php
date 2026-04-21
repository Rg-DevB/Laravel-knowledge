<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BestSolutionNotification extends Notification
{
    public function __construct(public readonly \App\Models\Solution $solution) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "Your solution was marked as Best Solution on \"{$this->solution->problem->title}\"! +25 reputation.",
            'url'     => route('problems.show', $this->solution->problem->slug),
            'type'    => 'best_solution',
        ];
    }
}
