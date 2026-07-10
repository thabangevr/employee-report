@extends('layouts.app')

@section('title', 'New Weekly Submission')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn active">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn">OKRs</a>
@endsection

@section('content')
    <h2 style="font-size:20px;font-weight:700;color:var(--ev-text);margin-bottom:24px;">
        New Weekly Update — Week of {{ $weekStartDate->format('d M Y') }}
    </h2>

    <form method="POST" action="{{ route('weekly-submissions.store') }}">
        @csrf
        <input type="hidden" name="week_start_date" value="{{ $weekStartDate->toDateString() }}">

        @include('weekly-submissions._form', [
            'okrs' => $okrs,
            'managerAreas' => $managerAreas,
            'selectedOkrIds' => [],
            'formAreas' => [['name' => '', 'manager_area_id' => null, 'status' => null, 'status_justification' => '', 'outcomes' => [['description' => '', 'okr_id' => '']], 'priorities' => [['description' => '', 'okr_id' => '']]]],
            'formFlags' => [],
            'formCrossTeamActions' => [],
        ])
    </form>
@endsection
