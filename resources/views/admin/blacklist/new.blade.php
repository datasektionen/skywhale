@extends('master')

@section('title', 'Skapa ny person')

@section('content')
{!! Form::open(['url' => URL::to(Request::path(), [], Request::secure())]) !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            KTH-mejl
        </span>
        <div class="input">
            {!! Form::text('kth_username', NULL, array('placeholder' => 'xxxxxxxx@kth.se')) !!}
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Blacklista e-post', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection
