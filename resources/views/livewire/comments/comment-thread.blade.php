<div class="space-y-3">
    @foreach($this->comments as $comment)
    <div class="flex gap-3" x-data="{ showReply: false }">
        <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=1e1e2e&color=a78bfa&size=32' }}"
             class="w-7 h-7 rounded-full shrink-0" alt="{{ $comment->user->name }}">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-medium text-zinc-300">{{ $comment->user->name }}</span>
                <span class="text-xs text-zinc-600">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-sm text-zinc-400">{{ $comment->content }}</p>
            <button @click="showReply = !showReply" class="text-xs text-zinc-600 hover:text-zinc-400 mt-1">Reply</button>

            @if($comment->replies->isNotEmpty())
            <div class="mt-3 pl-4 border-l border-zinc-800 space-y-3">
                @foreach($comment->replies as $reply)
                <div class="flex gap-2">
                    <img src="{{ $reply->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=1e1e2e&color=a78bfa&size=24' }}"
                         class="w-5 h-5 rounded-full shrink-0" alt="{{ $reply->user->name }}">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-zinc-400">{{ $reply->user->name }}</span>
                            <span class="text-xs text-zinc-600">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-zinc-500 mt-0.5">{{ $reply->content }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="pt-3 border-t border-zinc-800/60">
        <textarea wire:model="newComment" rows="2" placeholder="Write a comment..."
                  class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/50"></textarea>
        <button wire:click="postComment"
                class="mt-2 px-4 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-400 text-white text-xs font-medium transition-all">
            Post Comment
        </button>
    </div>
</div>
