@extends('master')

@section('title', 'Ändra post: ' . $position->name)

@section('content')
{!! Form::model($position) !!}
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
            {!! Form::hidden('position', 1) !!}
            {!! Form::submit('Uppdatera post', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection