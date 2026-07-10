@extends('layouts.app')

@section('title', 'Edit Weekly Submission')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn active">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn">OKRs</a>
@endsection

@section('content')
    <h2 style="font-size:20px;font-weight:700;color:var(--ev-text);margin-bottom:24px;">
        Edit Weekly Update — Week of {{ $submission->week_start_date->format('d M Y') }}
    </h2>

    <form method="POST" action="{{ route('weekly-submissions.update', $submission) }}">
        @csrf
        @method('PUT')

        @include('weekly-submissions._form', [
            'okrs' => $okrs,
            'managerAreas' => $managerAreas,
            'selectedOkrIds' => $submission->okrFocus->pluck('id')->toArray(),
            'formAreas' => $submission->areas->map(fn($area) => [
                'name' => $area->name,
                'manager_area_id' => $area->manager_area_id,
                'status' => $area->status?->value,
                'status_justification' => $area->status_justification ?? '',
                'outcomes' => $area->outcomes->map(fn($o) => [
                    'description' => $o->description,
                    'okr_id' => $o->okr_id ?? '',
                ])->toArray(),
                'priorities' => $area->priorities->map(fn($p) => [
                    'description' => $p->description,
                    'okr_id' => $p->okr_id ?? '',
                ])->toArray(),
            ])->toArray(),
            'formFlags' => $submission->flags->map(fn($f) => [
                'risk' => $f->risk,
                'cause' => $f->cause,
                'consequence' => $f->consequence,
            ])->toArray(),
            'formCrossTeamActions' => $submission->crossTeamActions->map(fn($c) => [
                'owner_name' => $c->owner_name,
                'ask' => $c->ask,
            ])->toArray(),
        ])
    </form>
@endsection
