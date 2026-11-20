@extends('master')

@section('title', 'Dina inställningar')

@section('content')
<form method="POST"  action="{{ request()->getPathInfo() }}">
    @csrf

    <div class="form">
        <div class="form-entry">
            <span class="description">
                Vill du få ett e-postmeddelande när du blir nominerad?
            </span>
            <div class="input">
                <div class="radio">
                    <input
                        type="radio"
                        name="wants_email"
                        id="email_yes"
                        value="yes"
                        @checked(old('wants_email', $user->wants_email) === 'yes')
                    >
                    <label for="email_yes">Ja</label>
                </div>
                <div class="radio">
                    <input
                        type="radio"
                        name="wants_email"
                        id="email_no"
                        value="no"
                        @checked(old('wants_email', $user->wants_email) === 'no')
                    >
                    <label for="email_no">Nej</label>
                </div>
            </div>
        </div>

        <div class="form-entry">
            <div class="input">
                <button type="submit">Spara</button>
            </div>
        </div>
    </div>
</form>
@endsection
