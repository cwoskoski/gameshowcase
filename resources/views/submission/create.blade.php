@extends('layouts.layout')

<?php
    $types = \App\Submission::getTypes();
    $langs = [];
    foreach(['English', 'Spanish', 'French'] as $lang)
    {
        $langs[$lang] = $lang;
    }
?>

@section('content')

    <h2>Submit New {{ $types[$type] }}</h2>

    {!! Form::open(['url' => 'submission']) !!}

        <input type="hidden" name="type" value="{{ $type }}" />

        <div class="form-group">
            {!! Form::label('title', 'Title:') !!}
            {!! Form::text('title', null, ['class' => 'form-control']) !!}
            <span class="text-danger">{{ $errors->first('title') }}</span>
        </div>

        <div class="form-group">
            {!! Form::label('description', 'Description:') !!}
            {!! Form::textarea('description', null, ['class' => 'form-control editor']) !!}
            <span class="text-danger">{{ $errors->first('description') }}</span>
        </div>

        <?php
        if ($type == 'game') {
            $categories = config('site.categories');
        } else if ($type == 'resource') {
            $categories = config('site.resource_categories');
        }
        ?>
        @if ($type != 'tutorial')
            <div class="form-group">
                {!! Form::label('category_id', 'Category:') !!}
                {!! Form::select('category_id', $categories, null, ['class' => 'form-control']) !!}
                <span class="text-danger">{{ $errors->first('category_id') }}</span>
            </div>
        @endif

        <div class="form-group">
            {!! Form::label('lang[]', 'Supported Languages:') !!}
            {!! Form::select('lang[]', config('site.languages'), 0, ['class' => 'form-control', 'multiple' => 'multiple']) !!}
            <span class="text-danger">{{ $errors->first('lang[]') }}</span>
        </div>

        <div class="form-group">
            {!! Form::submit('Add ' . ucwords($type), ['class' => 'btn btn-primary form-control']) !!}
        </div>
    {!! Form::close() !!}

@stop