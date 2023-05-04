@extends('master')

@section('title', 'Skapa ny person')

@section('content')
{!! Form::open(['url' => URL::to(Request::path(), [], Request::secure())]) !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Namn:
        </span>
        <div class="input">
            {!! Form::text('name', NULL, array('placeholder' => 'Namn')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            KTH-id
        </span>
        <div class="input">
            {!! Form::text('kth_user_id', NULL, array('placeholder' => 'uXXXXXXXX')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            KTH-mejl
        </span>
        <div class="input">
            {!! Form::text('kth_username', NULL, array('placeholder' => 'xxxxxxxx@kth.se')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Årskurs
        </span>
        <div class="input">
            {!! Form::text('year', NULL, array('placeholder' => 'DXX')) !!}
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Skapa person', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection
