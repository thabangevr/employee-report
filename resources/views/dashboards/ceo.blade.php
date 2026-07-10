@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn active">Executive Summary</a>
@endsection

@push('styles')
<style>
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 14px;
        margin-bottom: 28px;
    }
    .metric-card {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .metric-card .value {
        font-size: 32px;
        font-weight: 800;
        line-height: 1.1;
    }
    .metric-card .label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--ev-text-secondary);
    }
    .metric-card .sub {
        font-size: 11px;
        color: #9aabb8;
        margin-top: 2px;
    }
    .manager-card {
        border: 1px solid var(--ev-border);
        border-radius: 10px;
        background: var(--ev-white);
        overflow: hidden;
        transition: box-shadow 0.15s;
    }
    .manager-card:hover {
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .manager-card-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--ev-border-light);
    }
    .manager-card-body {
        padding: 14px 20px;
    }
    .manager-name {
        font-size: 15px;
        font-weight: 700;
        color: var(--ev-text);
    }
    .manager-title {
        font-size: 11px;
        color: var(--ev-text-secondary);
        margin-top: 2px;
    }
    .area-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .area-status-dot.green { background: var(--ev-green); }
    .area-status-dot.amber { background: var(--ev-amber); }
    .area-status-dot.blocker { background: var(--ev-red); }
    .mini-stat {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--ev-text-secondary);
    }
    .mini-stat .num {
        font-weight: 700;
        font-size: 14px;
        color: var(--ev-text);
    }
    .flag-row {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 12px 16px;
        border-left: 4px solid var(--ev-red);
        background: var(--ev-white);
        border-radius: 0 8px 8px 0;
        margin-bottom: 10px;
    }
    .flag-manager-tag {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        background: var(--ev-dark);
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }
    .no-submission-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: var(--ev-bg);
        color: var(--ev-text-secondary);
        border: 1px solid var(--ev-border);
    }
    .progress-bar-track {
        height: 6px;
        background: var(--ev-border);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 8px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.3s;
    }
    @media (max-width: 700px) {
        .metric-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')
    <div style="margin-bottom:22px;">
        <p style="font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ev-green);font-weight:700;margin-bottom:4px;">Executive Overview</p>
        <h2 style="font-size:20px;font-weight:700;color:var(--ev-dark);margin-bottom:2px;">{{ $user->name }}</h2>
        <p style="font-size:13px;color:var(--ev-text-secondary);">{{ $user->job_title ?? 'CEO' }}</p>
    </div>

    {{-- Metrics --}}
    <div class="metric-grid">
        <div class="ev-card metric-card">
            <span class="label">Managers</span>
            <span class="value" style="color:var(--ev-dark);">{{ $metrics['total_managers'] }}</span>
            <span class="sub">{{ $metrics['submitted_this_week'] }} submitted this week</span>
        </div>
        <div class="ev-card metric-card">
            <span class="label">Green Areas</span>
            <span class="value" style="color:var(--ev-green-value);">{{ $metrics['green_areas'] }}</span>
            <span class="sub">On track</span>
        </div>
        <div class="ev-card metric-card">
            <span class="label">Amber Areas</span>
            <span class="value" style="color:var(--ev-amber-value);">{{ $metrics['amber_areas'] }}</span>
            <span class="sub">Monitoring required</span>
        </div>
        <div class="ev-card metric-card">
            <span class="label">Blockers</span>
            <span class="value" style="color:var(--ev-red);">{{ $metrics['blockers'] }}</span>
            <span class="sub">Require exec action</span>
        </div>
        <div class="ev-card metric-card">
            <span class="label">Total Flags</span>
            <span class="value" style="color:var(--ev-amber-value);">{{ $metrics['total_flags'] }}</span>
            <span class="sub">Across all managers</span>
        </div>
    </div>

    {{-- Submission Progress --}}
    @if($metrics['total_managers'] > 0)
        <div class="ev-card" style="margin-bottom:28px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ev-text-heading);">
                        Weekly Submission Progress
                    </span>
                    <span style="font-size:13px;color:var(--ev-text-secondary);margin-left:12px;">
                        {{ $metrics['submitted_this_week'] }} / {{ $metrics['total_managers'] }} managers submitted
                    </span>
                </div>
                <span style="font-size:18px;font-weight:700;color:var(--ev-green);">
                    {{ $metrics['total_managers'] > 0 ? round(($metrics['submitted_this_week'] / $metrics['total_managers']) * 100) : 0 }}%
                </span>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width:{{ $metrics['total_managers'] > 0 ? round(($metrics['submitted_this_week'] / $metrics['total_managers']) * 100) : 0 }}%;background:var(--ev-green);"></div>
            </div>
        </div>
    @endif

    {{-- Flags --}}
    <p class="section-title">Flags & Risks</p>
    @if($allFlags->isNotEmpty())
        <div style="margin-bottom:28px;">
            @foreach($allFlags as $flag)
                <div class="flag-row">
                    <div style="flex-shrink:0;">
                        <span class="flag-manager-tag">{{ $flag->weeklySubmission->user->name ?? 'Unknown' }}</span>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:600;color:var(--ev-red);margin-bottom:4px;">{{ $flag->risk }}</div>
                        <div style="font-size:13px;color:var(--ev-text-body);">
                            <strong>Cause:</strong> {{ $flag->cause }}
                        </div>
                        <div style="font-size:13px;color:var(--ev-text-body);">
                            <strong>Consequence:</strong> {{ $flag->consequence }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="ev-card" style="margin-bottom:28px;">
            <p style="font-size:12px;color:#b0bec8;font-style:italic;margin:0;">No flags raised this period.</p>
        </div>
    @endif

    {{-- Manager Cards --}}
    <p class="section-title">Manager Status</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(380px,1fr));gap:14px;margin-bottom:28px;">
        @forelse($managerSummaries as $summary)
            @php $sub = $summary['submission']; @endphp
            <div class="manager-card">
                <div class="manager-card-header">
                    <div>
                        <div class="manager-name">{{ $summary['manager']->name }}</div>
                        <div class="manager-title">{{ $summary['manager']->job_title ?? 'Manager' }}</div>
                    </div>
                    @if($sub)
                        <span class="pill {{ $sub->isDraft() ? 'pill-amber' : 'pill-green' }}">
                            {{ $sub->status->label() }}
                        </span>
                    @else
                        <span class="no-submission-badge">No Submission</span>
                    @endif
                </div>
                <div class="manager-card-body">
                    @if($sub)
                        <div style="font-size:12px;color:var(--ev-text-secondary);margin-bottom:10px;">
                            Week of {{ $sub->week_start_date->format('d M Y') }}
                            @if($sub->submitted_at)
                                &mdash; submitted {{ $sub->submitted_at->format('d M H:i') }}
                            @endif
                        </div>

                        {{-- Area dots --}}
                        @if($sub->areas->isNotEmpty())
                            <div style="margin-bottom:10px;">
                                @foreach($sub->areas as $area)
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                        @if($area->status)
                                            <span class="area-status-dot {{ $area->status->value }}"></span>
                                        @else
                                            <span class="area-status-dot" style="background:var(--ev-border);"></span>
                                        @endif
                                        <span style="font-size:13px;color:var(--ev-text-body);">{{ $area->name }}</span>
                                        @if($area->status)
                                            <span class="pill {{ $area->status->pillClass() }}" style="font-size:10px;padding:1px 7px;">{{ $area->status->label() }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Mini stats --}}
                        <div class="d-flex gap-3 flex-wrap" style="margin-bottom:12px;">
                            <div class="mini-stat">
                                <span class="num">{{ $sub->word_count }}</span> words
                            </div>
                            <div class="mini-stat">
                                <span class="num">{{ $sub->flags->count() }}</span> flags
                            </div>
                            <div class="mini-stat">
                                <span class="num">{{ $sub->okrFocus->count() }}</span> OKRs
                            </div>
                            @if($sub->comments->isNotEmpty())
                                <div class="mini-stat">
                                    <span class="num">{{ $sub->comments->count() }}</span> comments
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('ceo.manager.detail', $summary['manager']) }}" class="btn-ev-outline" style="width:100%;text-align:center;display:block;padding:8px;font-size:13px;">
                            View Details
                        </a>
                    @else
                        <p style="font-size:13px;color:var(--ev-text-secondary);margin:10px 0;">No weekly submission found for this manager.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="ev-card" style="text-align:center;padding:40px 20px;grid-column:1/-1;">
                <p style="font-size:13px;color:var(--ev-text-secondary);margin:0;">No managers found. Assign the manager role to users to see them here.</p>
            </div>
        @endforelse
    </div>
@endsection
