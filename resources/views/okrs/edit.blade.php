@extends('layouts.app')

@section('title', 'Edit OKR')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn active">OKRs</a>
@endsection

@section('content')
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('okrs.index') }}" style="color:var(--ev-text-secondary);text-decoration:none;font-size:13px;font-weight:600;">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="vertical-align:-2px;margin-right:4px;">
                <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to OKRs
        </a>
    </div>

    <h2 style="font-size:20px;font-weight:700;color:var(--ev-text);margin:0 0 4px;">Edit OKR</h2>
    <p style="font-size:13px;color:var(--ev-text-secondary);margin:0 0 24px;">Update your objective, key results, and success criteria.</p>

    <form method="POST" action="{{ route('okrs.update', $okr) }}">
        @csrf
        @method('PUT')
        @include('okrs._form', ['okr' => $okr])
    </form>
@endsection
