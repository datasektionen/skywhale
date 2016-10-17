@extends('master')

@section('title', 'Skapa ny post')

@section('content')
{!! Form::open() !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Namn på posten:
        </span>
        <div class="input">
            {!! Form::text('name', NULL, array('placeholder' => 'T.ex. Ordförande')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Beskrivning av posten:
        </span>
        <div class="input">
            {!! Form::textarea('description', NULL, array('placeholder' => 'T.ex. "Det är kul att vara ordförande!"')) !!}
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Skapa post', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection