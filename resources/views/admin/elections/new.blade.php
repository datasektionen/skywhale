@extends('master')

@section('title', 'Skapa nytt val')

@section('content')
{!! Form::open() !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Namn på valtillfället:
        </span>
        <div class="input">
            {!! Form::text('name', NULL, array('placeholder' => 'T.ex. Budget-SM')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Beskrivning av valtillfället:
        </span>
        <div class="input">
            {!! Form::textarea('description', NULL, array('placeholder' => 'T.ex. "Det väljs massa roliga poster, kom och lek!"')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Nominering öppnar:
        </span>
        <div class="input">
            {!! Form::input('datetime-local', 'opens', NULL) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Nomineringsstopp:
        </span>
        <div class="input">
            {!! Form::input('datetime-local', 'nomination_stop', NULL) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Acceptansstopp:
        </span>
        <div class="input">
            {!! Form::input('datetime-local', 'acceptance_stop', NULL) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Valet stänger (när det inte längre visas på valsidan):
        </span>
        <div class="input">
            {!! Form::input('datetime-local', 'closes', NULL) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Poster som kan väljas
        </span>
        <div class="input">
            @foreach (\App\Models\Position::all() as $position)
                <div class="checkbox">
                    {{ Form::checkbox('positions[]', $position->identifier, false, array('id' => 'position-' . $position->identifier )) }} 
                    <label for="position-{{ $position->identifier }}">{{ $position->title }}</label>
                </div>
            @endforeach
            <p>Du kan lägga till fler poster senare.</p>
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::hidden('election', 1) !!}
            {!! Form::submit('Lys val', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection