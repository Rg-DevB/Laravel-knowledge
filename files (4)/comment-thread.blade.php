{{-- resources/views/livewire/comments/comment-thread.blade.php --}}
<div>
    {{-- Comments list --}}
    @if($this->comments->isNotEmpty())
    <div class="space-y-3 mb-4">
        @foreach($this->comments as $comment)
        <div wire:key="comment-{{ $comment->id }}" class="flex gap-3">
            <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=1e1e2e&color=a78bfa&size=32' }}"
                 class="w-6 h-6 rounded-full flex-shrink-0 mt-0.5" alt="{{ $comment->user->name }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('profile.show', $comment->user->username) }}" wire:navigate
                       class="text-xs font-medium text-zinc-300 hover:text-white transition-colors">{{ $comment->user->name }}</a>
                    <span class="text-xs text-zinc-600">{{ $comment->created_at->diffForHumans() }}</span>
                    @auth
                    <button wire:click="replyTo({{ $comment->id }}, '{{ $comment->user->name }}')"
                            class="text-xs text-zinc-600 hover:text-zinc-400 transition-colors ml-auto">Reply</button>
                    @endauth
                </div>
                <p class="text-xs text-zinc-400 leading-relaxed">{{ $comment->content }}</p>

                {{-- Replies --}}
                @if($comment->replies->isNotEmpty())
                <div class="mt-2 pl-3 border-l border-zinc-800 space-y-2">
                    @foreach($comment->replies as $reply)
                    <div wire:key="reply-{{ $reply->id }}" class="flex gap-2">
                        <img src="{{ $reply->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=1e1e2e&color=a78bfa&size=24' }}"
                             class="w-5 h-5 rounded-full flex-shrink-0 mt-0.5" alt="{{ $reply->user->name }}">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <a href="{{ route('profile.show', $reply->user->username) }}" wire:navigate
                                   class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">{{ $reply->user->name }}</a>
                                <span class="text-xs text-zinc-600">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-zinc-500 leading-relaxed">{{ $reply->content }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- New comment form --}}
    @auth
    <div>
        @if($replyTo)
        <div class="flex items-center gap-2 mb-2 text-xs text-zinc-500">
            <span>Replying to <strong class="text-zinc-400">{{ $replyToName }}</strong></span>
            <button wire:click="cancelReply" class="text-zinc-600 hover:text-zinc-400 transition-colors">✕ Cancel</button>
        </div>
        @endif
        <div class="flex gap-2">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1e1e2e&color=a78bfa&size=32' }}"
                 class="w-6 h-6 rounded-full flex-shrink-0 mt-1" alt="you">
            <div class="flex-1 flex gap-2">
                <input wire:model.defer="newComment"
                       wire:keydown.enter.prevent="postComment"
                       type="text"
                       placeholder="{{ $replyTo ? 'Write a reply…' : 'Add a comment…' }}"
                       class="flex-1 bg-zinc-900/60 border border-zinc-700/60 focus:border-rose-500/50 focus:ring-1 focus:ring-rose-500/20 rounded-xl px-3 py-1.5 text-xs text-zinc-200 placeholder-zinc-600 outline-none transition-all">
                <button wire:click="postComment"
                        class="px-3 py-1.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-xs text-zinc-400 hover:text-zinc-200 transition-all flex-shrink-0">
                    Post
                </button>
            </div>
        </div>
    </div>
    @else
    <p class="text-xs text-zinc-600">
        <a href="{{ route('login') }}" class="text-rose-400 hover:text-rose-300 transition-colors">Sign in</a> to add a comment.
    </p>
    @endauth
</div>
