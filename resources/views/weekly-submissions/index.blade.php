@extends('layouts.app')

@section('title', 'Weekly Submissions')

@section('tabs')
    <a href="{{ route('dashboard') }}" class="tab-btn">Dashboard</a>
    <a href="{{ route('weekly-submissions.index') }}" class="tab-btn active">Weekly Updates</a>
    <a href="{{ route('okrs.index') }}" class="tab-btn">OKRs</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-size:20px;font-weight:700;color:var(--ev-text);margin:0;">Weekly Submissions</h2>
        <a href="{{ route('weekly-submissions.create') }}" class="btn-ev-primary">+ New Submission</a>
    </div>

    @if($submissions->isEmpty())
        <div class="ev-card text-center py-5">
            <p style="color:var(--ev-text-secondary);font-size:15px;margin-bottom:16px;">No weekly submissions yet.</p>
            <a href="{{ route('weekly-submissions.create') }}" class="btn-ev-primary">Create Your First Update</a>
        </div>
    @else
        <div class="ev-card" style="padding:0;overflow:hidden;">
            <table class="table table-hover mb-0" style="font-size:14px;">
                <thead>
                    <tr style="background:var(--ev-bg);">
                        <th style="padding:12px 20px;font-weight:600;color:var(--ev-text-heading);border:none;">Week</th>
                        <th style="padding:12px 20px;font-weight:600;color:var(--ev-text-heading);border:none;">Status</th>
                        <th style="padding:12px 20px;font-weight:600;color:var(--ev-text-heading);border:none;">Words</th>
                        <th style="padding:12px 20px;font-weight:600;color:var(--ev-text-heading);border:none;">Submitted</th>
                        <th style="padding:12px 20px;font-weight:600;color:var(--ev-text-heading);border:none;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                        <tr>
                            <td style="padding:12px 20px;vertical-align:middle;">
                                {{ $submission->week_start_date->format('d M Y') }}
                            </td>
                            <td style="padding:12px 20px;vertical-align:middle;">
                                <span class="pill {{ $submission->isDraft() ? 'pill-amber' : 'pill-green' }}">
                                    {{ $submission->status->label() }}
                                </span>
                            </td>
                            <td style="padding:12px 20px;vertical-align:middle;">
                                {{ $submission->word_count }}
                            </td>
                            <td style="padding:12px 20px;vertical-align:middle;color:var(--ev-text-secondary);">
                                {{ $submission->submitted_at?->format('d M Y H:i') ?? '—' }}
                            </td>
                            <td style="padding:12px 20px;vertical-align:middle;text-align:right;">
                                <a href="{{ route('weekly-submissions.show', $submission) }}" class="btn-ev-outline" style="padding:6px 14px;font-size:12px;">
                                    View
                                </a>
                                @if($submission->isDraft())
                                    <a href="{{ route('weekly-submissions.edit', $submission) }}" class="btn-ev-outline" style="padding:6px 14px;font-size:12px;">
                                        Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
