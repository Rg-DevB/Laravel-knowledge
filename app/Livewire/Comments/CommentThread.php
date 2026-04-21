<?php

namespace App\Livewire\Comments;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CommentThread extends Component
{
    #[Reactive]
    public Model  $commentable;

    public string $newComment = '';
    public ?int   $replyTo = null;
    public string $replyToName = '';

    public function getCommentsProperty()
    {
        return $this->commentable
            ->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->get();
    }

    public function replyTo(int $commentId, string $username): void
    {
        $this->replyTo = $commentId;
        $this->replyToName = $username;
    }

    public function cancelReply(): void
    {
        $this->replyTo = null;
        $this->replyToName = '';
    }

    public function postComment(): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');
            return;
        }

        $this->validate(['newComment' => 'required|min:5|max:2000']);

        $this->commentable->comments()->create([
            'user_id'   => auth()->id(),
            'content'   => $this->newComment,
            'parent_id' => $this->replyTo,
        ]);

        $this->commentable->increment('comments_count');
        $this->reset(['newComment', 'replyTo', 'replyToName']);
        $this->dispatch('comment-added');
    }

    public function render()
    {
        return view('livewire.comments.comment-thread');
    }
}
