@extends('master')

@section('title', 'Dina inställningar')

@section('content')
{!! Form::open(['url' => URL::to(Request::path(), [], Request::secure())]) !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Vill du få ett e-postmeddelande när du bli nominerad?
        </span>
        <div class="input">
            <div class="radio">
                {!! Form::radio('wants_email', 'yes', $user->wants_email === 'yes', array('id' => 'email_yes')) !!}
                <label for="email_yes">Ja</label>
            </div>
            <div class="radio">
                {!! Form::radio('wants_email', 'no', $user->wants_email === 'no', array('id' => 'email_no')) !!}
                <label for="email_no">Nej</label>
            </div>
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Spara', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection
