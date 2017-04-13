<!-- -->
@extends('layouts.layout')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>Use the following links to login / register:
                    {!!Html::link('/login','Login',['class'=>'btn btn-link'])!!}/{!!Html::link('/register','Register',['class'=>'btn btn-link'])!!}
                </p>
            </div>
        </div>
    </div>

@stop