@extends('master')

@section('title', 'Logga in')

@section('content')
{!! Form::open() !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Visa sidan som om du hade adressen nedan.
        </span>
        <div class="input">
            {!! Form::text('username', NULL, array('placeholder' => 'uid@kth.se')) !!}
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Byt användare') !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection