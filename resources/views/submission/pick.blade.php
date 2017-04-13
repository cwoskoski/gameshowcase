@extends('layouts.layout')

@section('content')

    <h2>What would you like to add?</h2>

    <div class="row submission-type-picker">
        <div class="col-sm-4">{!! Html::link('/submission/create/game', 'Game', ['class' => 'btn btn-block btn-primary game']) !!}</div>
        <div class="col-sm-4">{!! Html::link('/submission/create/resource', 'Resource', ['class' => 'btn btn-block btn-primary resource']) !!}</div>
        <div class="col-sm-4">{!! Html::link('/submission/create/tutorial', 'Tutorial', ['class' => 'btn btn-block btn-primary tutorial']) !!}</div>
    </div>

@stop