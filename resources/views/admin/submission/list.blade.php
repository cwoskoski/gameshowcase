@extends('layouts.layout')

@section('content')

    <h2>Submissions</h2>

    <ul class="list-group">
        @forelse($submissions as $submission)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-8">{{ $submission->title }}</div>
                    <div class="col-sm-4 text-right">
                        <div class="btn-group" role="group" aria-label="...">
                            <a class="btn btn-default btn-sm" href="{{ route('submission', $submission->id) }}">View</a>
                            @if ($submission->approved != 1)
                                <a class="btn btn-default btn-sm" href="{{ route('admin.approve', $submission->id) }}">Approve</a>
                                <a class="btn btn-default btn-sm deny-submission" href="{{ route('admin.deny', $submission->id) }}">Deny</a>
                            @else
                                <a class="btn btn-default btn-sm" href="{{ route('admin.suspend', $submission->id) }}">Suspend</a>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item">No submissions</li>
        @endforelse
    </ul>

@stop