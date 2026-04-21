<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    public function __construct(public readonly \App\Models\Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "{$this->comment->user->name} commented on your problem.",
            'url'     => '#',
            'type'    => 'comment',
        ];
    }
}
