<div id="comment-{{ $comment->id }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="font-semibold text-slate-900">{{ $comment->user?->name ?? __('Guest') }}</div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500 mt-1">
                {{ $comment->created_at?->format('M d, Y H:i') }}
            </div>
        </div>
    </div>

    <div class="prose prose-slate max-w-none mt-4">
        <p>{{ $comment->content }}</p>
    </div>

    @auth
        @if (auth()->user()->hasVerifiedEmail())
            <details class="mt-4">
                <summary class="cursor-pointer text-sm font-medium text-slate-700">{{ __('Reply') }}</summary>
                <form action="{{ route('blog.comments.store', $slug) }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="content" rows="3" class="w-full rounded-xl border border-slate-200 p-3" placeholder="{{ __('Write a reply') }}"></textarea>
                    <button type="submit" class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm">{{ __('Reply') }}</button>
                </form>
            </details>
        @endif
    @endauth

    @if ($comment->childrenRecursive->isNotEmpty())
        <div class="mt-5 space-y-4 border-l border-slate-200 pl-4">
            @foreach ($comment->childrenRecursive as $child)
                @include('frontend.blog.partials.comment', ['comment' => $child, 'slug' => $slug])
            @endforeach
        </div>
    @endif
</div>
