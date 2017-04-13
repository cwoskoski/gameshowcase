<!-- -->
@extends('layouts.layout')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>
                    Welcome to the Dashboard, use {!!Html::link('/logout','Logout',['class'=>'btn btn-link'])!!} to logout.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <ul>
                    <li>{!!Html::link('/game','My Games')!!}</li>
                    <li>{!!Html::link('/game/create','Add Game')!!}</li>
                </ul>
            </div>
            <div class="col-md-9 col-sm-12">
                main
            </div>
        </div>
    </div>

@stop