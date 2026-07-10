@extends('layouts.app')

@section('title', 'Weekly Update — ' . $submission->week_start_date->format('d M Y'))

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn active">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn">OKRs</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:var(--ev-text);margin:0;">
                Week of {{ $submission->week_start_date->format('d M Y') }}
            </h2>
            <span class="pill {{ $submission->isDraft() ? 'pill-amber' : 'pill-green' }}" style="margin-top:6px;">
                {{ $submission->status->label() }}
            </span>
            <span style="font-size:12px;color:var(--ev-text-secondary);margin-left:8px;">
                {{ $submission->word_count }} words
            </span>
        </div>
        <div class="d-flex gap-2">
            @if($submission->isDraft())
                <a href="{{ route('weekly-submissions.edit', $submission) }}" class="btn-ev-outline">Edit</a>
                <form method="POST" action="{{ route('weekly-submissions.submit', $submission) }}">
                    @csrf
                    <button type="submit" class="btn-ev-primary" onclick="return confirm('Submit this update? It cannot be edited afterwards.')">
                        Submit
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- OKR Focus --}}
    @if($submission->okrFocus->isNotEmpty())
        <div class="section-title">OKR Focus This Week</div>
        <div class="ev-card mb-4">
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
        <div class="section-title">Headline Number</div>
        <div class="ev-card mb-4">
            <div style="font-size:28px;font-weight:700;color:var(--ev-green);">{{ $submission->one_number_value }}</div>
            @if($submission->one_number_label)
                <div style="font-size:13px;color:var(--ev-text-secondary);">{{ $submission->one_number_label }}</div>
            @endif
        </div>
    @endif

    {{-- Areas --}}
    @foreach($submission->areas as $area)
        <div class="section-title">{{ $area->name }}</div>
        <div class="ev-card mb-4">
            @if($area->status)
                <div class="mb-3">
                    <span class="pill {{ $area->status->pillClass() }}">{{ $area->status->label() }}</span>
                    @if($area->status_justification)
                        <span style="font-size:13px;color:var(--ev-text-body);margin-left:8px;">— {{ $area->status_justification }}</span>
                    @endif
                </div>
            @endif

            @if($area->outcomes->isNotEmpty())
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ev-text-heading);margin-bottom:8px;">
                    Last Week Outcomes
                </div>
                <ul style="margin-bottom:16px;padding-left:18px;">
                    @foreach($area->outcomes as $outcome)
                        <li style="margin-bottom:6px;">
                            {{ $outcome->description }}
                            @if($outcome->okr)
                                <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:1px 7px;border-radius:10px;font-size:10px;font-weight:600;margin-left:4px;">
                                    {{ $outcome->okr->title }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($area->priorities->isNotEmpty())
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ev-text-heading);margin-bottom:8px;">
                    This Week Priorities
                </div>
                <ul style="margin-bottom:0;padding-left:18px;">
                    @foreach($area->priorities as $priority)
                        <li style="margin-bottom:6px;">
                            {{ $priority->description }}
                            @if($priority->okr)
                                <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:1px 7px;border-radius:10px;font-size:10px;font-weight:600;margin-left:4px;">
                                    {{ $priority->okr->title }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach

    {{-- Flags --}}
    @if($submission->flags->isNotEmpty())
        <div class="section-title">Flags</div>
        <div class="ev-card mb-4">
            @foreach($submission->flags as $flag)
                <div style="margin-bottom:{{ $loop->last ? '0' : '16px' }};padding-bottom:{{ $loop->last ? '0' : '16px' }};border-bottom:{{ $loop->last ? 'none' : '1px solid var(--ev-border-light)' }};">
                    <div style="font-size:12px;font-weight:600;color:var(--ev-red);margin-bottom:4px;">Risk</div>
                    <div style="margin-bottom:8px;">{{ $flag->risk }}</div>
                    <div style="font-size:12px;font-weight:600;color:var(--ev-amber);margin-bottom:4px;">Cause</div>
                    <div style="margin-bottom:8px;">{{ $flag->cause }}</div>
                    <div style="font-size:12px;font-weight:600;color:var(--ev-text-heading);margin-bottom:4px;">Consequence</div>
                    <div>{{ $flag->consequence }}</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Cross-team Actions --}}
    @if($submission->crossTeamActions->isNotEmpty())
        <div class="section-title">Cross-team Actions</div>
        <div class="ev-card mb-4">
            @foreach($submission->crossTeamActions as $action)
                <div style="margin-bottom:{{ $loop->last ? '0' : '12px' }};">
                    <strong style="color:var(--ev-text-heading);">{{ $action->owner_name }}:</strong>
                    {{ $action->ask }}
                </div>
            @endforeach
        </div>
    @endif

    {{-- Comments --}}
    @php $submission->load('comments.user'); @endphp
    <div class="section-title">Comments</div>
    <div class="ev-card">
        @if($submission->comments->isNotEmpty())
            @foreach($submission->comments as $comment)
                <div style="padding:12px 0;border-bottom:{{ $loop->last ? 'none' : '1px solid var(--ev-border-light)' }};">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <strong style="font-size:13px;color:var(--ev-text);">{{ $comment->user->name }}</strong>
                            <span style="font-size:11px;color:var(--ev-text-secondary);margin-left:8px;">{{ $comment->created_at->diffForHumans() }}</span>
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
                    <div style="font-size:14px;color:var(--ev-text-body);margin-top:6px;line-height:1.6;">{{ $comment->body }}</div>
                </div>
            @endforeach
        @else
            <p style="font-size:13px;color:var(--ev-text-secondary);margin:0 0 14px;">No comments yet.</p>
        @endif

        <form method="POST" action="{{ route('submission-comments.store', $submission) }}" style="margin-top:14px;border-top:1px solid var(--ev-border-light);padding-top:14px;">
            @csrf
            <textarea name="body" style="border:1px solid var(--ev-border);border-radius:8px;padding:12px 14px;font-size:14px;color:var(--ev-text);width:100%;resize:vertical;min-height:80px;font-family:inherit;" placeholder="Add a comment..." required></textarea>
            <div class="d-flex justify-content-end" style="margin-top:10px;">
                <button type="submit" class="btn-ev-primary" style="padding:8px 20px;font-size:13px;">Post Comment</button>
            </div>
        </form>
    </div>
@endsection
