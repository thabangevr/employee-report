@extends('layouts.app')

@section('title', 'My OKRs')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn active">OKRs</a>
@endsection

@push('styles')
<style>
    .okr-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid var(--ev-border-light);
    }
    .okr-item:last-child { border-bottom: none; }
    .okr-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--ev-text);
    }
    .okr-inactive .okr-title {
        color: var(--ev-text-secondary);
        text-decoration: line-through;
    }
    .okr-actions {
        display: flex;
        align-items: center;
        gap: 8px;
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
    }
    .btn-icon:hover { border-color: var(--ev-green); color: var(--ev-green); }
    .btn-icon.danger:hover { border-color: var(--ev-red); color: var(--ev-red); }
    .add-okr-input {
        border: 1px solid var(--ev-border);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        color: var(--ev-text);
        width: 100%;
        transition: border-color 0.15s;
    }
    .add-okr-input:focus {
        border-color: var(--ev-green);
        outline: none;
        box-shadow: 0 0 0 3px rgba(108,191,62,0.12);
    }
    .edit-input {
        border: 1px solid var(--ev-green);
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 14px;
        color: var(--ev-text);
        flex: 1;
    }
    .edit-input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(108,191,62,0.12);
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:var(--ev-text);margin:0;">My OKRs</h2>
            <p style="font-size:13px;color:var(--ev-text-secondary);margin:4px 0 0;">Manage your objectives — these are used to tag outcomes and priorities in weekly updates.</p>
        </div>
    </div>

    {{-- Add OKR Form --}}
    <div class="ev-card" style="margin-bottom:20px;">
        <form method="POST" action="{{ route('okrs.store') }}" class="d-flex gap-3 align-items-end">
            @csrf
            <div style="flex:1;">
                <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ev-text-heading);margin-bottom:6px;display:block;">
                    Add New OKR
                </label>
                <input type="text" name="title" class="add-okr-input" placeholder="e.g. Improve platform uptime to 99.9%" required
                    value="{{ old('title') }}">
            </div>
            <button type="submit" class="btn-ev-primary" style="white-space:nowrap;">+ Add OKR</button>
        </form>
        @error('title')
            <div style="color:var(--ev-red);font-size:12px;margin-top:8px;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Active OKRs --}}
    @php
        $activeOkrs = $okrs->where('is_active', true);
        $inactiveOkrs = $okrs->where('is_active', false);
    @endphp

    <p class="section-title">Active OKRs ({{ $activeOkrs->count() }})</p>
    <div class="ev-card" style="margin-bottom:24px;padding:0;overflow:hidden;" x-data="{ editing: null }">
        @forelse($activeOkrs as $okr)
            <div class="okr-item">
                {{-- Display mode --}}
                <template x-if="editing !== {{ $okr->id }}">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center gap-3">
                            <span style="background:var(--ev-tag-bg);color:var(--ev-tag-text);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                                OKR
                            </span>
                            <span class="okr-title">{{ $okr->title }}</span>
                        </div>
                        <div class="okr-actions">
                            <button type="button" class="btn-icon" @click="editing = {{ $okr->id }}">Edit</button>
                            <form method="POST" action="{{ route('okrs.update', $okr) }}" style="margin:0;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $okr->title }}">
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit" class="btn-icon" title="Deactivate">Deactivate</button>
                            </form>
                            <form method="POST" action="{{ route('okrs.destroy', $okr) }}" style="margin:0;"
                                onsubmit="return confirm('Delete this OKR? It will be untagged from all submissions.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon danger" title="Delete">Delete</button>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- Edit mode --}}
                <template x-if="editing === {{ $okr->id }}">
                    <form method="POST" action="{{ route('okrs.update', $okr) }}" class="d-flex align-items-center gap-3 w-100">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_active" value="1">
                        <input type="text" name="title" class="edit-input" value="{{ $okr->title }}" required>
                        <button type="submit" class="btn-ev-primary" style="padding:8px 18px;font-size:13px;">Save</button>
                        <button type="button" class="btn-ev-outline" style="padding:8px 14px;font-size:13px;" @click="editing = null">Cancel</button>
                    </form>
                </template>
            </div>
        @empty
            <div style="padding:30px 20px;text-align:center;">
                <p style="font-size:13px;color:var(--ev-text-secondary);margin:0;">No active OKRs yet. Add your first one above.</p>
            </div>
        @endforelse
    </div>

    {{-- Inactive OKRs --}}
    @if($inactiveOkrs->isNotEmpty())
        <p class="section-title">Inactive OKRs ({{ $inactiveOkrs->count() }})</p>
        <div class="ev-card" style="padding:0;overflow:hidden;" x-data="{ editing: null }">
            @foreach($inactiveOkrs as $okr)
                <div class="okr-item okr-inactive">
                    <template x-if="editing !== {{ $okr->id }}">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center gap-3">
                                <span style="background:var(--ev-bg);color:var(--ev-text-secondary);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                                    OKR
                                </span>
                                <span class="okr-title">{{ $okr->title }}</span>
                            </div>
                            <div class="okr-actions">
                                <form method="POST" action="{{ route('okrs.update', $okr) }}" style="margin:0;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="title" value="{{ $okr->title }}">
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="btn-icon">Reactivate</button>
                                </form>
                                <form method="POST" action="{{ route('okrs.destroy', $okr) }}" style="margin:0;"
                                    onsubmit="return confirm('Delete this OKR permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>
    @endif
@endsection
