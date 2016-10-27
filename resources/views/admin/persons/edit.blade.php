@extends('master')

@section('title', 'Ändra person: ' . $person->name)

@section('action-button')
    <a class="primary-action" href="/admin/persons/remove/{{ $person->id }}">Ta bort</a>
@endsection

@section('content')
{!! Form::model($person, ['url' => URL::to(Request::path(), [], true)]) !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Namn på personen:
        </span>
        <div class="input">
            {!! Form::text('name', NULL, array('placeholder' => 'Namn')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            KTH-användarid:
        </span>
        <div class="input">
            {!! Form::text('kth_user_id', NULL, array('placeholder' => 'KTH-kth_user_id')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            KTH-användarnamn:
        </span>
        <div class="input">
            {!! Form::text('kth_username', NULL, array('placeholder' => 'KTH-användarnamn')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Årskurs:
        </span>
        <div class="input">
            {!! Form::text('year', NULL, array('placeholder' => 'Ex D-13')) !!}
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Uppdatera person', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection