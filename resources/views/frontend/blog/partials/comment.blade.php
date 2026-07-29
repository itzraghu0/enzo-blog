@php
    $initials = collect(explode(' ', (string) ($comment->user?->name ?? __('Guest'))))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp

<div id="comment-{{ $comment->id }}" class="editorial-card p-5 md:p-6">
    <div class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[color:var(--primary-soft)] text-sm font-bold text-[color:var(--primary)]">
            {{ $initials ?: 'G' }}
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <div class="font-bold text-[color:var(--text)]">{{ $comment->user?->name ?? __('Guest') }}</div>
                <span class="text-xs uppercase tracking-[0.2em] text-[color:var(--muted)]">
                    {{ $comment->created_at?->format('M d, Y H:i') }}
                </span>
            </div>

            <div class="mt-3 text-sm leading-7 text-[color:var(--muted)]">
                {!! nl2br(e($comment->content)) !!}
            </div>

            @auth
                @if (auth()->user()->hasVerifiedEmail())
                    <details class="mt-4">
                        <summary class="cursor-pointer text-sm font-semibold text-[color:var(--primary)]">{{ __('Reply') }}</summary>
                        <form action="{{ route('blog.comments.store', $slug) }}" method="POST" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <textarea name="content" rows="3" class="editorial-textarea" placeholder="{{ __('Write a reply') }}"></textarea>
                            <button type="submit" class="editorial-button editorial-button-primary">{{ __('Reply') }}</button>
                        </form>
                    </details>
                @endif
            @endauth

            @if ($comment->childrenRecursive->isNotEmpty())
                <div class="mt-5 space-y-4 border-l-2 border-[color:var(--border)] pl-4 md:pl-6">
                    @foreach ($comment->childrenRecursive as $child)
                        @include('frontend.blog.partials.comment', ['comment' => $child, 'slug' => $slug])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
