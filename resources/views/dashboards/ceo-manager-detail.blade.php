@extends('layouts.app')

@section('title', $manager->name . ' — Manager Detail')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Executive Summary</a>
@endsection

@push('styles')
<style>
    .detail-sidebar {
        position: sticky;
        top: 20px;
    }
    .history-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-bottom: 1px solid var(--ev-border-light);
        font-size: 13px;
        text-decoration: none;
        color: var(--ev-text-body);
        transition: background 0.1s;
    }
    .history-item:hover { background: var(--ev-bg); }
    .history-item:last-child { border-bottom: none; }
    .history-item.active { background: var(--ev-highlight-bg); border-left: 3px solid var(--ev-green); }
    .comment-item {
        padding: 14px 0;
        border-bottom: 1px solid var(--ev-border-light);
    }
    .comment-item:last-child { border-bottom: none; }
    .comment-author {
        font-weight: 700;
        font-size: 13px;
        color: var(--ev-text);
    }
    .comment-time {
        font-size: 11px;
        color: var(--ev-text-secondary);
        margin-left: 8px;
    }
    .comment-body {
        font-size: 14px;
        color: var(--ev-text-body);
        margin-top: 6px;
        line-height: 1.6;
    }
    .comment-input {
        border: 1px solid var(--ev-border);
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 14px;
        color: var(--ev-text);
        width: 100%;
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
    }
    .comment-input:focus {
        border-color: var(--ev-green);
        outline: none;
        box-shadow: 0 0 0 3px rgba(108,191,62,0.12);
    }
    .area-section {
        border-left: 4px solid var(--ev-border);
        padding-left: 16px;
        margin-bottom: 20px;
    }
    .area-section.green { border-color: var(--ev-green); }
    .area-section.amber { border-color: var(--ev-amber); }
    .area-section.blocker { border-color: var(--ev-red); }
</style>
@endpush

@section('content')
    {{-- Breadcrumb --}}
    <div style="margin-bottom:20px;">
        <a href="{{ route('dashboard') }}" style="font-size:12px;color:var(--ev-text-secondary);text-decoration:none;">
            Executive Dashboard
        </a>
        <span style="font-size:12px;color:var(--ev-text-secondary);margin:0 6px;">/</span>
        <span style="font-size:12px;color:var(--ev-text);font-weight:600;">{{ $manager->name }}</span>
    </div>

    <div class="row g-4">
        {{-- Main content --}}
        <div class="col-lg-8">
            {{-- Manager header --}}
            <div class="ev-card" style="margin-bottom:20px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2 style="font-size:20px;font-weight:700;color:var(--ev-dark);margin:0;">{{ $manager->name }}</h2>
                        <p style="font-size:13px;color:var(--ev-text-secondary);margin:4px 0 0;">{{ $manager->job_title ?? 'Manager' }}</p>
                    </div>
                    @if($submission)
                        <span class="pill {{ $submission->isDraft() ? 'pill-amber' : 'pill-green' }}">
                            {{ $submission->status->label() }}
                        </span>
                    @endif
                </div>
            </div>

            @if($submission)
                <div style="font-size:12px;color:var(--ev-text-secondary);margin-bottom:16px;">
                    Week of <strong>{{ $submission->week_start_date->format('d M Y') }}</strong>
                    @if($submission->submitted_at)
                        &mdash; submitted {{ $submission->submitted_at->format('d M Y \a\t H:i') }}
                    @endif
                    &mdash; {{ $submission->word_count }} words
                </div>

                {{-- OKR Focus --}}
                @if($submission->okrFocus->isNotEmpty())
                    <p class="section-title">OKR Focus</p>
                    <div class="ev-card" style="margin-bottom:20px;">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($submission->okrFocus as $okr)
                                <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                                    {{ $okr->title }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Headline Number --}}
                @if($submission->one_number_value)
                    <p class="section-title">Headline Number</p>
                    <div class="ev-card" style="margin-bottom:20px;">
                        <div style="font-size:28px;font-weight:700;color:var(--ev-green);">{{ $submission->one_number_value }}</div>
                        @if($submission->one_number_label)
                            <div style="font-size:13px;color:var(--ev-text-secondary);">{{ $submission->one_number_label }}</div>
                        @endif
                    </div>
                @endif

                {{-- Areas --}}
                @foreach($submission->areas as $area)
                    <div class="area-section {{ $area->status?->value ?? '' }}">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h3 style="font-size:15px;font-weight:700;color:var(--ev-text);margin:0;">{{ $area->name }}</h3>
                            @if($area->status)
                                <span class="pill {{ $area->status->pillClass() }}">{{ $area->status->label() }}</span>
                            @endif
                        </div>
                        @if($area->status_justification)
                            <p style="font-size:13px;color:var(--ev-text-body);margin-bottom:12px;font-style:italic;">{{ $area->status_justification }}</p>
                        @endif

                        @if($area->outcomes->isNotEmpty())
                            <div style="margin-bottom:12px;">
                                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ev-text-heading);">
                                    Last Week Outcomes
                                </span>
                                <ul style="margin:6px 0 0;padding-left:18px;">
                                    @foreach($area->outcomes as $outcome)
                                        <li style="margin-bottom:4px;font-size:14px;color:var(--ev-text-body);">
                                            {{ $outcome->description }}
                                            @if($outcome->okr)
                                                <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:1px 7px;border-radius:10px;font-size:10px;font-weight:600;">
                                                    {{ $outcome->okr->title }}
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($area->priorities->isNotEmpty())
                            <div>
                                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ev-text-heading);">
                                    This Week Priorities
                                </span>
                                <ul style="margin:6px 0 0;padding-left:18px;">
                                    @foreach($area->priorities as $priority)
                                        <li style="margin-bottom:4px;font-size:14px;color:var(--ev-text-body);">
                                            {{ $priority->description }}
                                            @if($priority->okr)
                                                <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:1px 7px;border-radius:10px;font-size:10px;font-weight:600;">
                                                    {{ $priority->okr->title }}
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Flags --}}
                @if($submission->flags->isNotEmpty())
                    <p class="section-title">Flags</p>
                    <div style="margin-bottom:20px;">
                        @foreach($submission->flags as $flag)
                            <div style="border-left:4px solid var(--ev-red);padding:12px 16px;background:var(--ev-white);border-radius:0 8px 8px 0;margin-bottom:10px;">
                                <div style="font-size:14px;font-weight:600;color:var(--ev-red);margin-bottom:4px;">{{ $flag->risk }}</div>
                                <div style="font-size:13px;color:var(--ev-text-body);"><strong>Cause:</strong> {{ $flag->cause }}</div>
                                <div style="font-size:13px;color:var(--ev-text-body);"><strong>Consequence:</strong> {{ $flag->consequence }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Cross-team Actions --}}
                @if($submission->crossTeamActions->isNotEmpty())
                    <p class="section-title">Cross-team Actions</p>
                    <div class="ev-card" style="margin-bottom:20px;">
                        @foreach($submission->crossTeamActions as $action)
                            <div style="padding:8px 0;border-bottom:{{ $loop->last ? 'none' : '1px solid var(--ev-border-light)' }};">
                                <strong style="color:var(--ev-text-heading);">{{ $action->owner_name }}:</strong>
                                <span style="color:var(--ev-text-body);">{{ $action->ask }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Comments --}}
                <p class="section-title">Comments</p>
                <div class="ev-card" style="margin-bottom:20px;">
                    @if($submission->comments->isNotEmpty())
                        @foreach($submission->comments as $comment)
                            <div class="comment-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="comment-author">{{ $comment->user->name }}</span>
                                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($comment->user_id === auth()->id())
                                        <form method="POST" action="{{ route('submission-comments.destroy', $comment) }}" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:none;border:none;font-size:11px;color:var(--ev-text-secondary);cursor:pointer;" onclick="return confirm('Delete this comment?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="comment-body">{{ $comment->body }}</div>
                            </div>
                        @endforeach
                    @else
                        <p style="font-size:13px;color:var(--ev-text-secondary);margin:0 0 14px;">No comments yet.</p>
                    @endif

                    <form method="POST" action="{{ route('submission-comments.store', $submission) }}" style="margin-top:14px;border-top:1px solid var(--ev-border-light);padding-top:14px;">
                        @csrf
                        <textarea name="body" class="comment-input" placeholder="Add a comment for {{ $manager->name }}..." required></textarea>
                        @error('body')
                            <div style="color:var(--ev-red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-end" style="margin-top:10px;">
                            <button type="submit" class="btn-ev-primary" style="padding:8px 20px;font-size:13px;">Post Comment</button>
                        </div>
                    </form>
                </div>

            @else
                <div class="ev-card" style="text-align:center;padding:40px 20px;">
                    <p style="font-size:14px;color:var(--ev-text-secondary);margin:0;">This manager has not submitted any weekly updates yet.</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="detail-sidebar">
                <p class="section-title">Submission History</p>
                <div class="ev-card" style="padding:0;overflow:hidden;">
                    @forelse($submissions as $sub)
                        <a href="{{ route('ceo.submission.review', $sub) }}"
                            class="history-item {{ $submission && $sub->id === $submission->id ? 'active' : '' }}">
                            <span>{{ $sub->week_start_date->format('d M Y') }}</span>
                            <span class="pill {{ $sub->isDraft() ? 'pill-amber' : 'pill-green' }}" style="font-size:10px;padding:1px 7px;">
                                {{ $sub->status->label() }}
                            </span>
                        </a>
                    @empty
                        <div style="padding:20px;text-align:center;">
                            <p style="font-size:13px;color:var(--ev-text-secondary);margin:0;">No submissions.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
