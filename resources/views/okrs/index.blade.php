@extends('layouts.app')

@section('title', 'My OKRs')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn active">OKRs</a>
@endsection

@push('styles')
<style>
    .weight-bar-container {
        background: var(--ev-white);
        border: 1px solid var(--ev-border);
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 24px;
    }
    .weight-bar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .weight-bar-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--ev-text-heading);
    }
    .weight-bar-value {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .weight-bar-track {
        background: var(--ev-bg);
        border-radius: 6px;
        height: 10px;
        overflow: hidden;
    }
    .weight-bar-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.4s ease;
    }
    .weight-bar-hint {
        font-size: 12px;
        margin-top: 8px;
    }

    .okr-card {
        background: var(--ev-white);
        border: 1px solid var(--ev-border);
        border-radius: 10px;
        margin-bottom: 14px;
        overflow: hidden;
        transition: border-color 0.15s;
    }
    .okr-card:hover {
        border-color: #c8d8e8;
    }
    .okr-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 18px 20px;
        cursor: pointer;
        gap: 16px;
    }
    .okr-card-left {
        flex: 1;
        min-width: 0;
    }
    .okr-card-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }
    .okr-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--ev-text);
    }
    .okr-card-inactive .okr-card-title {
        color: var(--ev-text-secondary);
    }
    .weight-badge {
        font-size: 12px;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 20px;
        background: var(--ev-green-bg);
        color: var(--ev-green-value);
        white-space: nowrap;
    }
    .okr-card-inactive .weight-badge {
        background: var(--ev-bg);
        color: var(--ev-text-secondary);
    }
    .okr-card-meta {
        font-size: 13px;
        color: var(--ev-text-secondary);
        line-height: 1.5;
    }
    .okr-card-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .okr-card-body {
        padding: 0 20px 18px;
        border-top: 1px solid var(--ev-border-light);
    }
    .okr-card-body-inner {
        padding-top: 16px;
    }
    .okr-detail-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--ev-text-secondary);
        margin-bottom: 6px;
    }
    .okr-detail-value {
        font-size: 14px;
        color: var(--ev-text-body);
        line-height: 1.5;
    }
    .kr-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .kr-list li {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 6px 0;
        font-size: 13px;
        color: var(--ev-text-body);
        border-bottom: 1px solid var(--ev-border-light);
    }
    .kr-list li:last-child {
        border-bottom: none;
    }
    .kr-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-top: 6px;
        flex-shrink: 0;
    }
    .kr-dot-lag { background: var(--ev-tag-text); }
    .kr-dot-lead { background: var(--ev-green); }
    .kr-empty {
        font-size: 12px;
        color: var(--ev-text-secondary);
        font-style: italic;
        padding: 4px 0;
    }

    .btn-icon {
        background: none;
        border: 1px solid var(--ev-border);
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 12px;
        color: var(--ev-text-secondary);
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .btn-icon:hover { border-color: var(--ev-green); color: var(--ev-green); }
    .btn-icon.danger:hover { border-color: var(--ev-red); color: var(--ev-red); }

    .expand-icon {
        transition: transform 0.2s ease;
        color: var(--ev-text-secondary);
    }
    .expand-icon.open {
        transform: rotate(180deg);
    }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
    }
    .empty-state-icon {
        width: 56px;
        height: 56px;
        background: var(--ev-highlight-bg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    @media (max-width: 700px) {
        .okr-card-header {
            flex-direction: column;
        }
        .okr-card-right {
            width: 100%;
            justify-content: flex-end;
            margin-top: 8px;
        }
    }
</style>
@endpush

@section('content')
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:var(--ev-text);margin:0;">My OKRs</h2>
            <p style="font-size:13px;color:var(--ev-text-secondary);margin:4px 0 0;">Manage your objectives, key results, and success criteria.</p>
        </div>
        <a href="{{ route('okrs.create') }}" class="btn-ev-primary">+ New OKR</a>
    </div>

    {{-- Weight Allocation Bar --}}
    @if($activeOkrs->isNotEmpty())
        @php
            $barColor = $totalWeight === 100
                ? 'var(--ev-green)'
                : ($totalWeight > 100 ? 'var(--ev-red)' : 'var(--ev-amber)');
            $barWidth = min($totalWeight, 100);
        @endphp
        <div class="weight-bar-container">
            <div class="weight-bar-header">
                <span class="weight-bar-label">Weight Allocation</span>
                <span class="weight-bar-value" style="color: {{ $barColor }}">{{ $totalWeight }}%</span>
            </div>
            <div class="weight-bar-track">
                <div class="weight-bar-fill" style="width: {{ $barWidth }}%; background: {{ $barColor }};"></div>
            </div>
            @if($totalWeight < 100)
                <div class="weight-bar-hint" style="color: var(--ev-amber-value);">
                    {{ 100 - $totalWeight }}% unallocated — your active OKR weights should total 100%.
                </div>
            @elseif($totalWeight > 100)
                <div class="weight-bar-hint" style="color: var(--ev-red);">
                    Over-allocated by {{ $totalWeight - 100 }}% — reduce weights so they total 100%.
                </div>
            @else
                <div class="weight-bar-hint" style="color: var(--ev-green-value);">
                    Perfectly balanced — your OKR weights total 100%.
                </div>
            @endif
        </div>
    @endif

    {{-- Active OKRs --}}
    <div class="section-title">Active OKRs ({{ $activeOkrs->count() }})</div>

    @forelse($activeOkrs as $okr)
        <div class="okr-card" x-data="{ expanded: false }">
            <div class="okr-card-header" @click="expanded = !expanded">
                <div class="okr-card-left">
                    <div class="okr-card-title-row">
                        <span class="okr-card-title">{{ $okr->title }}</span>
                        <span class="weight-badge">{{ $okr->weight }}%</span>
                    </div>
                    @if($okr->measure_of_success)
                        <div class="okr-card-meta">
                            <strong style="font-weight:600;">Success:</strong> {{ $okr->measure_of_success }}
                        </div>
                    @endif
                    @php
                        $lagCount = $okr->keyResults->where('type', 'lag_measure')->count();
                        $leadCount = $okr->keyResults->where('type', 'lead_measure')->count();
                    @endphp
                    @if($lagCount || $leadCount)
                        <div class="okr-card-meta" style="margin-top:2px;">
                            @if($lagCount)
                                <span style="color:var(--ev-tag-text);">{{ $lagCount }} lag</span>
                            @endif
                            @if($lagCount && $leadCount)
                                <span style="margin:0 4px;">·</span>
                            @endif
                            @if($leadCount)
                                <span style="color:var(--ev-green-value);">{{ $leadCount }} lead</span>
                            @endif
                            measures
                        </div>
                    @endif
                </div>
                <div class="okr-card-right" @click.stop>
                    <a href="{{ route('okrs.edit', $okr) }}" class="btn-icon">Edit</a>
                    <form method="POST" action="{{ route('okrs.toggle', $okr) }}" style="margin:0;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-icon">Deactivate</button>
                    </form>
                    <form method="POST" action="{{ route('okrs.destroy', $okr) }}" style="margin:0;"
                        onsubmit="return confirm('Delete this OKR? It will be untagged from all submissions.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon danger">Delete</button>
                    </form>
                    <svg class="expand-icon" :class="{ open: expanded }" width="18" height="18" viewBox="0 0 18 18" fill="none" @click="expanded = !expanded" style="cursor:pointer;">
                        <path d="M5 7l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            <div x-show="expanded" x-collapse>
                <div class="okr-card-body">
                    <div class="okr-card-body-inner">
                        @if($okr->objective_description)
                            <div class="mb-3">
                                <div class="okr-detail-label">Objective Description</div>
                                <div class="okr-detail-value">{{ $okr->objective_description }}</div>
                            </div>
                        @endif

                        <div class="row g-3">
                            {{-- Lag Measures --}}
                            <div class="col-md-6">
                                <div class="okr-detail-label">
                                    <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;margin-right:4px;">LAG</span>
                                    Outcomes
                                </div>
                                @php $lagMeasures = $okr->keyResults->where('type', 'lag_measure'); @endphp
                                @if($lagMeasures->isNotEmpty())
                                    <ul class="kr-list">
                                        @foreach($lagMeasures as $kr)
                                            <li>
                                                <span class="kr-dot kr-dot-lag"></span>
                                                <span>{{ $kr->description }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="kr-empty">No lag measures defined</div>
                                @endif
                            </div>

                            {{-- Lead Measures --}}
                            <div class="col-md-6">
                                <div class="okr-detail-label">
                                    <span style="background:var(--ev-green-bg);color:var(--ev-green-value);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;margin-right:4px;">LEAD</span>
                                    Predictive Actions
                                </div>
                                @php $leadMeasures = $okr->keyResults->where('type', 'lead_measure'); @endphp
                                @if($leadMeasures->isNotEmpty())
                                    <ul class="kr-list">
                                        @foreach($leadMeasures as $kr)
                                            <li>
                                                <span class="kr-dot kr-dot-lead"></span>
                                                <span>{{ $kr->description }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="kr-empty">No lead measures defined</div>
                                @endif
                            </div>
                        </div>

                        @if($okr->measure_of_success)
                            <div class="mt-3" style="background:var(--ev-highlight-bg);border:1px solid var(--ev-highlight-border);border-radius:8px;padding:12px 16px;">
                                <div class="okr-detail-label" style="margin-bottom:2px;">Measure of Success</div>
                                <div class="okr-detail-value" style="font-weight:600;">{{ $okr->measure_of_success }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="ev-card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <path d="M14 5v18M5 14h18" stroke="var(--ev-green)" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 style="font-size:16px;font-weight:700;color:var(--ev-text);margin:0 0 6px;">No OKRs yet</h3>
                <p style="font-size:13px;color:var(--ev-text-secondary);margin:0 0 16px;">Create your first OKR to start tracking objectives and key results.</p>
                <a href="{{ route('okrs.create') }}" class="btn-ev-primary">+ Create Your First OKR</a>
            </div>
        </div>
    @endforelse

    {{-- Inactive OKRs --}}
    @if($inactiveOkrs->isNotEmpty())
        <div class="section-title" style="margin-top:32px;">Inactive OKRs ({{ $inactiveOkrs->count() }})</div>

        @foreach($inactiveOkrs as $okr)
            <div class="okr-card okr-card-inactive" x-data="{ expanded: false }">
                <div class="okr-card-header" @click="expanded = !expanded">
                    <div class="okr-card-left">
                        <div class="okr-card-title-row">
                            <span style="background:var(--ev-bg);color:var(--ev-text-secondary);padding:3px 10px;border-radius:12px;font-size:10px;font-weight:700;text-transform:uppercase;">Inactive</span>
                            <span class="okr-card-title">{{ $okr->title }}</span>
                            <span class="weight-badge">{{ $okr->weight }}%</span>
                        </div>
                    </div>
                    <div class="okr-card-right" @click.stop>
                        <form method="POST" action="{{ route('okrs.toggle', $okr) }}" style="margin:0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-icon">Reactivate</button>
                        </form>
                        <form method="POST" action="{{ route('okrs.destroy', $okr) }}" style="margin:0;"
                            onsubmit="return confirm('Delete this OKR permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon danger">Delete</button>
                        </form>
                        <svg class="expand-icon" :class="{ open: expanded }" width="18" height="18" viewBox="0 0 18 18" fill="none" @click="expanded = !expanded" style="cursor:pointer;">
                            <path d="M5 7l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                <div x-show="expanded" x-collapse>
                    <div class="okr-card-body">
                        <div class="okr-card-body-inner">
                            @if($okr->objective_description)
                                <div class="mb-3">
                                    <div class="okr-detail-label">Objective Description</div>
                                    <div class="okr-detail-value">{{ $okr->objective_description }}</div>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="okr-detail-label">
                                        <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;margin-right:4px;">LAG</span>
                                        Outcomes
                                    </div>
                                    @php $lagMeasures = $okr->keyResults->where('type', 'lag_measure'); @endphp
                                    @if($lagMeasures->isNotEmpty())
                                        <ul class="kr-list">
                                            @foreach($lagMeasures as $kr)
                                                <li><span class="kr-dot kr-dot-lag"></span><span>{{ $kr->description }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="kr-empty">No lag measures</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="okr-detail-label">
                                        <span style="background:var(--ev-green-bg);color:var(--ev-green-value);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;margin-right:4px;">LEAD</span>
                                        Predictive Actions
                                    </div>
                                    @php $leadMeasures = $okr->keyResults->where('type', 'lead_measure'); @endphp
                                    @if($leadMeasures->isNotEmpty())
                                        <ul class="kr-list">
                                            @foreach($leadMeasures as $kr)
                                                <li><span class="kr-dot kr-dot-lead"></span><span>{{ $kr->description }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="kr-empty">No lead measures</div>
                                    @endif
                                </div>
                            </div>

                            @if($okr->measure_of_success)
                                <div class="mt-3" style="background:var(--ev-highlight-bg);border:1px solid var(--ev-highlight-border);border-radius:8px;padding:12px 16px;">
                                    <div class="okr-detail-label" style="margin-bottom:2px;">Measure of Success</div>
                                    <div class="okr-detail-value" style="font-weight:600;">{{ $okr->measure_of_success }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
