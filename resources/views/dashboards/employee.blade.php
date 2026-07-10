@extends('layouts.app')

@section('title', 'My Dashboard')

@section('tabs')
    <a href="#" class="tab-btn active">My OKRs</a>
    <a href="#" class="tab-btn">Weekly Updates</a>
@endsection

@push('styles')
<style>
    .welcome-card {
        background: var(--ev-highlight-bg);
        border: 1px solid var(--ev-highlight-border);
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }
    .okr-empty-card {
        border: 2px dashed var(--ev-border);
        border-radius: 10px;
        padding: 40px 24px;
        text-align: center;
    }
    .update-timeline {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
</style>
@endpush

@section('content')
    <div class="welcome-card">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <span style="font-size:18px;font-weight:700;color:var(--ev-dark);">Welcome, {{ $user->name }}</span>
            <span class="pill pill-green">{{ $user->role->label() }}</span>
        </div>
        <p style="font-size:13px;color:var(--ev-text-heading);margin:0;line-height:1.5;">
            {{ $user->job_title ?? 'Team Member' }}
            @if ($user->manager)
                · Reporting to <strong>{{ $user->manager->name }}</strong>
            @endif
        </p>
    </div>

    <p class="section-title">My OKRs</p>
    <div class="okr-empty-card" style="margin-bottom:28px;">
        <div style="margin-bottom:12px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--ev-text-secondary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <p style="font-size:15px;font-weight:600;color:var(--ev-dark);margin-bottom:4px;">No OKRs submitted yet</p>
        <p style="font-size:13px;color:var(--ev-text-secondary);margin-bottom:16px;">Start by adding your objectives and key results for this cycle.</p>
        <a href="#" class="btn-ev-primary">Add OKRs</a>
    </div>

    <p class="section-title">Weekly Update History</p>
    <div class="ev-card">
        <div class="update-timeline">
            <div style="text-align:center;padding:20px 0;">
                <p style="font-size:13px;color:var(--ev-text-secondary);">Your weekly submissions will appear here as a timeline.</p>
            </div>
        </div>
    </div>

    @if ($user->manager)
        <p class="section-title" style="margin-top:28px;">My Manager</p>
        <div class="ev-card" style="display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:50%;background:var(--ev-dark);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                {{ substr($user->manager->name, 0, 1) }}
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--ev-dark);">{{ $user->manager->name }}</div>
                <div style="font-size:12px;color:var(--ev-text-secondary);">{{ $user->manager->job_title ?? 'Manager' }}</div>
            </div>
        </div>
    @endif
@endsection
