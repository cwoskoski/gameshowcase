@extends('layouts.layout')

<?php
$types = \App\Submission::getTypes();
?>

@section('content')

    <h2>Edit {{ $types[$submission->type] }}</h2>

    @if ($submission->denied_for)
        <div class="alert alert-danger">
            This submission was denied! The review team provided the following reason:<br />
            <em>{{ $submission->denied_for }}</em>
        </div>
    @endif

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a></li>
        <li role="presentation"><a href="#media" aria-controls="media" role="tab" data-toggle="tab">Media</a></li>
    </ul>

    {!! Form::open(['url' => 'submission/edit/'. $submission->id, 'class' => 'submission-edit', 'data-id' => $submission->id]) !!}
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="details">
            <div class="form-group">
                {!! Form::label('title', 'Title:') !!}
                {!! Form::text('title', $submission->title, ['class' => 'form-control']) !!}
                <span class="text-danger">{{ $errors->first('title') }}</span>
            </div>

            <div class="form-group">
                {!! Form::label('description', 'Description:') !!}
                {!! Form::textarea('description', $submission->description, ['class' => 'form-control editor']) !!}
                <span class="text-danger">{{ $errors->first('description') }}</span>
            </div>

            <?php
            if ($submission->type == 'game') {
                $categories = config('site.categories');
            } else if ($submission->type == 'resource') {
                $categories = config('site.resource_categories');
            }
            ?>
            @if ($submission->type != 'tutorial')
                <div class="form-group">
                    {!! Form::label('category_id', 'Category:') !!}
                    {!! Form::select('category_id', $categories, $submission->category_id, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('category_id') }}</span>
                </div>
            @endif

            @if ($submission->type == 'game')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('lang', 'Supported Languages:') !!}
                        @foreach (config('site.languages') as $index => $language)
                            <div class="checkbox">
                                <label for="language_{{ $index }}">
                                    {!! Form::checkbox('lang[]', $index, array_key_exists($index, $submission->languages()), ['id' => 'language_' . $index]) !!} {{ $language }}
                                </label>
                            </div>
                        @endforeach
                        <span class="text-danger">{{ $errors->first('lang[]') }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('meta_content', 'Submission Contents:') !!}
                        @foreach (config('site.content') as $index => $content)
                            <div class="checkbox">
                                <label for="content_{{ $index }}">
                                    {!! Form::checkbox('meta_content[]', $index, array_key_exists($index, $submission->contents()), ['id' => 'content_' . $index]) !!} {{ $content }}
                                </label>
                            </div>
                        @endforeach
                        <span class="text-danger">{{ $errors->first('meta_content[]') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('meta_status', 'Status:') !!}
                {!! Form::select('meta_status', ['Prototype' => 'Prototype', 'Full Game' => 'Full Game', 'Demo' => 'Demo', 'Alpha' => 'Alpha', 'Beta' => 'Beta'], $submission->getMeta('status'), ['class' => 'form-control']) !!}
                <span class="text-danger">{{ $errors->first('meta_status') }}</span>
            </div>
            @endif

            <div class="form-group">
                {!! Form::label('meta_version', 'Version:') !!}
                {!! Form::text('meta_version', $submission->getMeta('version'), ['class' => 'form-control']) !!}
                <span class="text-danger">{{ $errors->first('meta_version') }}</span>
            </div>

            <div class="form-group">
                <a href="{{ route('submission.delete', $submission->id) }}" class="btn btn-lg btn-danger delete-submission pull-right">Delete {{ $types[$submission->type] }}</a>
                {!! Form::submit('Save ' . $types[$submission->type], ['class' => 'btn btn-lg btn-primary']) !!}
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="media">
            <div class="well drop-resource">

            </div>

            <div>
                <ul class="list-group resource-list">
                    @forelse($media as $m)

                        <li class="list-group-item {{ $m->type }}" data-id="{{ $m->id }}">
                            <div class="row">
                                <div class="col-sm-3">
                                    @if($m->type == 'image')
                                        {!! Html::image('/submissions/'.$submission->id.'/media/'.$m->id.'/media', "", ['class' => 'img-responsive']) !!}
                                    @else
                                        {!! Html::link('/submissions/'.$submission->id.'/media/'.$m->id.'/media', 'Download ' . $m->file_path, ['target' => '_blank']) !!}
                                    @endif
                                </div>
                                <div class="col-sm-9 text-right">
                                    {!! Html::link('/submissions/'.$submission->id.'/media/'.$m->id.'/remove', 'Remove', ['class' => 'btn btn-danger']) !!}
                                </div>
                            </div>
                        </li>

                    @empty
                        <li class="list-group-item empty">No media</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@stop