@extends('layouts.layout')

<?php
$types = \App\Submission::getTypes();
?>

@section('content')

    <div class="row">
        <div class="col-md-8">
            <h2 style="margin-top: 0;">
                @if ($search)
                    Search results for <i>{{ $search }}</i>
                @else
                    {{ $editable ? 'My ' : '' }} {{ $type ? ucwords($type) . 's' : 'Submissions' }}</h2>
                @endif
        </div>
        <div class="col-md-4 text-right">
            @if (in_array($type, ['game', 'resource']) && !$search)
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter By: {{ $category == -1 ? 'All' : $categories[$category] }} <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('submissions', ['mine' => $editable, 'category' => -1, 'search' => $search, 'type' => $type, 'sort_by' => $sort_by, 'sort_order' => $sort_order]) }}">All</a></li>
                    @foreach ($categories as $key => $cat)
                        <li><a href="{{ route('submissions', ['mine' => $editable, 'category' => $key, 'search' => $search, 'type' => $type, 'sort_by' => $sort_by, 'sort_order' => $sort_order]) }}">{{ $cat }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Sort By <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('submissions', ['mine' => $editable, 'category' => $category, 'search' => $search, 'type' => $type, 'sort_by' => 'title', 'sort_order' => 'asc']) }}">Alphabetical</a></li>
                    <li><a href="{{ route('submissions', ['mine' => $editable, 'category' => $category, 'search' => $search, 'type' => $type, 'sort_by' => 'rating', 'sort_order' => 'desc']) }}">Highest Rating</a></li>
                    <li><a href="{{ route('submissions', ['mine' => $editable, 'category' => $category, 'search' => $search, 'type' => $type, 'sort_by' => 'rating', 'sort_order' => 'asc']) }}">Lowest Rating</a></li>
                    <li><a href="{{ route('submissions', ['mine' => $editable, 'category' => $category, 'search' => $search, 'type' => $type, 'sort_by' => 'created_at', 'sort_order' => 'desc']) }}">Newest</a></li>
                    <li><a href="{{ route('submissions', ['mine' => $editable, 'category' => $category, 'search' => $search, 'type' => $type, 'sort_by' => 'created_at', 'sort_order' => 'asc']) }}">Oldest</a></li>
                </ul>
            </div>
        </div>
    </div>

    <ul class="list-group">
        @forelse($submissions as $submission)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-xs-1 text-center"><span class="submission-type {{ $submission->type }}">{{ strtoupper(substr($submission->type, 0, 1)) }}</span></div>
                    <div class="col-xs-11 col-md-4">{{ $submission->title }}</div>
                    <div class="col-md-3 text-right"></div>
                    <div class="col-md-4 text-right">
                        @if ($editable)
                        <span class="label label-{{ $submission->approved ? 'success' : ($submission->denied_for ? 'danger' : 'default') }}" style="margin-right: 5px;">
                            {{ $submission->approved ? 'Approved' : ($submission->denied_for ? 'Denied' : 'Pending Approval') }}
                        </span>
                        @endif
                        <a class="btn btn-default btn-sm" href="{{ URL::action('SubmissionController@show', $submission->id) }}">View</a>
                        @if ($editable)
                            <a class="btn btn-default btn-sm" href="{{ URL::action('SubmissionController@modify', $submission->id) }}">Edit</a>
                        @endif
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item">No submissions found!</li>
        @endforelse
    </ul>

    @if (count($submissions) > 0)
    <div>
        {!! $submissions->render() !!}
    </div>
    @endif

@stop