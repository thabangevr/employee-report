@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn active">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn">OKRs</a>
@endsection

@push('styles')
<style>
    .mgr-metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .mgr-metric-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.1;
        color: var(--ev-dark);
    }
    .mgr-metric-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--ev-text-secondary);
        margin-top: 4px;
    }
    .status-pill-inline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .status-pill-inline.green {
        background: var(--ev-green-bg);
        color: var(--ev-green-value);
        border: 1px solid var(--ev-highlight-border);
    }
    .status-pill-inline.amber {
        background: var(--ev-amber-bg);
        color: var(--ev-amber-value);
        border: 1px solid var(--ev-amber);
    }
    .status-pill-inline.blocker {
        background: var(--ev-red-bg);
        color: var(--ev-red);
        border: 1px solid var(--ev-red);
    }
    .status-pill-inline.draft {
        background: var(--ev-amber-bg);
        color: var(--ev-amber-value);
        border: 1px solid var(--ev-amber);
    }
    .priority-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 16px;
        border-bottom: 1px solid var(--ev-border-light);
    }
    .priority-item:last-child { border-bottom: none; }
    .priority-num {
        width: 26px;
        height: 26px;
        min-width: 26px;
        border-radius: 50%;
        background: var(--ev-dark);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .flag-card {
        border-left: 4px solid var(--ev-red);
        padding: 14px 16px;
        margin-bottom: 10px;
        background: var(--ev-white);
        border-radius: 0 8px 8px 0;
    }
    .area-status-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--ev-border-light);
    }
    .area-status-row:last-child { border-bottom: none; }
    @media (max-width: 700px) {
        .mgr-metrics-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')
    {{-- Header --}}
    <div style="margin-bottom:22px;">
        <p style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ev-green);font-weight:700;margin-bottom:4px;">Weekly Summary</p>
        <h2 style="font-size:20px;font-weight:700;color:var(--ev-dark);margin-bottom:2px;">{{ $user->name }}</h2>
        <p style="font-size:13px;color:var(--ev-text-secondary);">{{ $user->job_title ?? 'Manager' }}</p>
    </div>

    @if($latestSubmission)
        {{-- Metrics --}}
        <div class="mgr-metrics-grid">
            <div class="ev-card">
                <div class="mgr-metric-value" style="color:{{ $latestSubmission->word_count >= 150 ? 'var(--ev-green)' : 'var(--ev-amber)' }};">
                    {{ $latestSubmission->word_count }}
                </div>
                <div class="mgr-metric-label">Word Count</div>
            </div>
            <div class="ev-card">
                <div class="mgr-metric-value" style="color:{{ $latestSubmission->flags->count() > 0 ? 'var(--ev-red)' : 'var(--ev-green)' }};">
                    {{ $latestSubmission->flags->count() }}
                </div>
                <div class="mgr-metric-label">Flags Raised</div>
            </div>
            <div class="ev-card">
                <div class="mgr-metric-value" style="color:var(--ev-green);">
                    {{ $latestSubmission->okrFocus->count() }}
                </div>
                <div class="mgr-metric-label">OKRs in Focus</div>
            </div>
            <div class="ev-card">
                <div class="mgr-metric-value">{{ $latestSubmission->one_number_value ?? '—' }}</div>
                <div class="mgr-metric-label">{{ $latestSubmission->one_number_label ?? 'Headline Number' }}</div>
            </div>
        </div>

        {{-- Status + Week Info --}}
        <div class="ev-card" style="margin-bottom:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ev-text-secondary);font-weight:700;margin-bottom:10px;">
                        Week of {{ $latestSubmission->week_start_date->format('d M Y') }}
                    </p>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <span class="status-pill-inline {{ $latestSubmission->isDraft() ? 'draft' : 'green' }}">
                            {{ $latestSubmission->status->label() }}
                        </span>
                        @if($latestSubmission->submitted_at)
                            <span style="font-size:13px;color:var(--ev-text-secondary);">
                                Submitted {{ $latestSubmission->submitted_at->format('d M Y \a\t H:i') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('weekly-submissions.show', $latestSubmission) }}" class="btn-ev-outline">View Full</a>
                    <a href="{{ route('weekly-submissions.create') }}" class="btn-ev-primary">+ New Weekly Update</a>
                </div>
            </div>
        </div>

        {{-- OKR Focus --}}
        @if($latestSubmission->okrFocus->isNotEmpty())
            <p class="section-title">OKR Focus</p>
            <div class="ev-card" style="margin-bottom:20px;">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($latestSubmission->okrFocus as $okr)
                        <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                            {{ $okr->title }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Area Statuses --}}
        @if($latestSubmission->areas->isNotEmpty())
            <p class="section-title">Area Status</p>
            <div class="ev-card" style="margin-bottom:20px;padding:4px 20px;">
                @foreach($latestSubmission->areas as $area)
                    <div class="area-status-row">
                        <span style="font-weight:600;color:var(--ev-text);min-width:180px;">{{ $area->name }}</span>
                        @if($area->status)
                            <span class="pill {{ $area->status->pillClass() }}">{{ $area->status->label() }}</span>
                        @endif
                        @if($area->status_justification)
                            <span style="font-size:13px;color:var(--ev-text-secondary);flex:1;">{{ Str::limit($area->status_justification, 80) }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- This Week Priorities --}}
        @php
            $allPriorities = $latestSubmission->areas->flatMap(function ($area) {
                return $area->priorities->map(function ($p) use ($area) {
                    $p->area_name = $area->name;
                    return $p;
                });
            });
        @endphp

        @if($allPriorities->isNotEmpty())
            <p class="section-title">This Week — Priorities</p>
            <div class="ev-card" style="margin-bottom:20px;padding:0;">
                @foreach($allPriorities as $priority)
                    <div class="priority-item">
                        <div class="priority-num">{{ $loop->iteration }}</div>
                        <div style="flex:1;">
                            <div style="font-size:14px;color:var(--ev-text-body);">{{ $priority->description }}</div>
                            <div style="font-size:11px;color:var(--ev-text-secondary);margin-top:4px;">
                                {{ $priority->area_name }}
                                @if($priority->okr)
                                    <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:1px 7px;border-radius:10px;font-size:10px;font-weight:600;margin-left:4px;">
                                        {{ $priority->okr->title }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Last Week Outcomes --}}
        @php
            $allOutcomes = $latestSubmission->areas->flatMap(function ($area) {
                return $area->outcomes->map(function ($o) use ($area) {
                    $o->area_name = $area->name;
                    return $o;
                });
            });
        @endphp

        @if($allOutcomes->isNotEmpty())
            <p class="section-title">Last Week — Outcomes</p>
            <div class="ev-card" style="margin-bottom:20px;padding:0;">
                @foreach($allOutcomes as $outcome)
                    <div class="priority-item">
                        <div style="width:8px;height:8px;min-width:8px;border-radius:50%;background:var(--ev-green);margin-top:6px;"></div>
                        <div style="flex:1;">
                            <div style="font-size:14px;color:var(--ev-text-body);">{{ $outcome->description }}</div>
                            <div style="font-size:11px;color:var(--ev-text-secondary);margin-top:4px;">
                                {{ $outcome->area_name }}
                                @if($outcome->okr)
                                    <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:1px 7px;border-radius:10px;font-size:10px;font-weight:600;margin-left:4px;">
                                        {{ $outcome->okr->title }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Flags --}}
        <p class="section-title">Flags</p>
        @if($latestSubmission->flags->isNotEmpty())
            <div style="margin-bottom:20px;">
                @foreach($latestSubmission->flags as $flag)
                    <div class="flag-card">
                        <div style="font-size:14px;font-weight:600;color:var(--ev-red);margin-bottom:6px;">{{ $flag->risk }}</div>
                        <div style="font-size:13px;color:var(--ev-text-body);margin-bottom:4px;">
                            <span style="font-weight:600;color:var(--ev-text-heading);">Cause:</span> {{ $flag->cause }}
                        </div>
                        <div style="font-size:13px;color:var(--ev-text-body);">
                            <span style="font-weight:600;color:var(--ev-text-heading);">Consequence:</span> {{ $flag->consequence }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="ev-card" style="margin-bottom:20px;">
                <p style="font-size:12px;color:#b0bec8;font-style:italic;margin:0;">No flags raised.</p>
            </div>
        @endif

        {{-- Cross-team Actions --}}
        @if($latestSubmission->crossTeamActions->isNotEmpty())
            <p class="section-title">Cross-team Actions</p>
            <div class="ev-card" style="margin-bottom:20px;">
                @foreach($latestSubmission->crossTeamActions as $action)
                    <div style="padding:8px 0;border-bottom:{{ $loop->last ? 'none' : '1px solid var(--ev-border-light)' }};">
                        <strong style="color:var(--ev-text-heading);">{{ $action->owner_name }}:</strong>
                        <span style="color:var(--ev-text-body);">{{ $action->ask }}</span>
                    </div>
                @endforeach
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="mgr-metrics-grid">
            <div class="ev-card">
                <div class="mgr-metric-value" style="color:var(--ev-text-secondary);">—</div>
                <div class="mgr-metric-label">Word Count</div>
            </div>
            <div class="ev-card">
                <div class="mgr-metric-value" style="color:var(--ev-text-secondary);">—</div>
                <div class="mgr-metric-label">Flags Raised</div>
            </div>
            <div class="ev-card">
                <div class="mgr-metric-value" style="color:var(--ev-text-secondary);">—</div>
                <div class="mgr-metric-label">OKRs in Focus</div>
            </div>
            <div class="ev-card">
                <div class="mgr-metric-value" style="color:var(--ev-text-secondary);">—</div>
                <div class="mgr-metric-label">Headline Number</div>
            </div>
        </div>

        <div class="ev-card" style="margin-bottom:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ev-text-secondary);font-weight:700;margin-bottom:10px;">Overall Status</p>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <span class="status-pill-inline draft">No Submission</span>
                        <span style="font-size:13px;color:var(--ev-text-secondary);">Submit your weekly update to populate this dashboard.</span>
                    </div>
                </div>
                <a href="{{ route('weekly-submissions.create') }}" class="btn-ev-primary">+ New Weekly Update</a>
            </div>
        </div>

        <p class="section-title">This Week — Priorities</p>
        <div class="ev-card" style="text-align:center;padding:30px 20px;margin-bottom:20px;">
            <p style="font-size:13px;color:var(--ev-text-secondary);margin:0;">Your priorities will appear here after you submit your weekly update.</p>
        </div>

        <p class="section-title">Flags</p>
        <div class="ev-card" style="margin-bottom:20px;">
            <p style="font-size:12px;color:#b0bec8;font-style:italic;margin:0;">No flags raised yet.</p>
        </div>
    @endif

    {{-- Direct Reports --}}
    <p class="section-title">Direct Reports</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
        @forelse ($user->directReports as $report)
            <div class="ev-card">
                <div style="font-size:15px;font-weight:700;color:var(--ev-dark);">{{ $report->name }}</div>
                <div style="font-size:11px;color:var(--ev-text-secondary);margin-top:2px;">{{ $report->job_title ?? 'Employee' }}</div>
            </div>
        @empty
            <div class="ev-card" style="text-align:center;padding:30px 20px;">
                <p style="font-size:13px;color:var(--ev-text-secondary);margin:0;">No direct reports found.</p>
            </div>
        @endforelse
    </div>
@endsection
