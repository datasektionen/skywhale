@extends('master')

@section('title', 'Ändra val: ' . $election->name)

@section('action-button')
    <a href="{{ url("/admin/elections/remove/{$election->id}") }}" class="primary-action">Ta bort</a>
@endsection

@section('content')
    @php
        $selectedPositions = old('positions', $positions->pluck('identifier')->all());
        $formatDateTime = fn ($value) => $value instanceof \DateTimeInterface
            ? $value->format('Y-m-d\TH:i')
            : ($value ? str_replace(' ', 'T', (string) $value) : '');
    @endphp

    <form method="POST" action="{{ url()->current() }}">
        @csrf

        <div class="form">
            <div class="form-entry">
                <label for="name" class="description">
                    Namn på valtillfället:
                </label>
                <div class="input">
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $election->name) }}"
                        placeholder="T.ex. Budget-SM"
                    >
                </div>
            </div>

            <div class="form-entry">
                <label for="description" class="description">
                    Beskrivning av valtillfället:
                </label>
                <div class="input">
                    <textarea
                        name="description"
                        id="description"
                        placeholder='T.ex. "Det väljs massa roliga poster, kom och lek!"'
                    >{{ old('description', $election->description) }}</textarea>
                </div>
            </div>

            <div class="form-entry">
                <label for="opens" class="description">
                    Nominering öppnar:
                </label>
                <div class="input">
                    <input
                        type="datetime-local"
                        name="opens"
                        id="opens"
                        value="{{ old('opens', $formatDateTime($election->opens)) }}"
                    >
                </div>
            </div>

            <div class="form-entry">
                <label for="nomination_stop" class="description">
                    Nomineringsstopp:
                </label>
                <div class="input">
                    <input
                        type="datetime-local"
                        name="nomination_stop"
                        id="nomination_stop"
                        value="{{ old('nomination_stop', $formatDateTime($election->nomination_stop)) }}"
                    >
                </div>
            </div>

            <div class="form-entry">
                <label for="acceptance_stop" class="description">
                    Acceptansstopp:
                </label>
                <div class="input">
                    <input
                        type="datetime-local"
                        name="acceptance_stop"
                        id="acceptance_stop"
                        value="{{ old('acceptance_stop', $formatDateTime($election->acceptance_stop)) }}"
                    >
                </div>
            </div>

            <div class="form-entry">
                <label for="closes" class="description">
                    Valet stänger (när det inte längre visas på valsidan):
                </label>
                <div class="input">
                    <input
                        type="datetime-local"
                        name="closes"
                        id="closes"
                        value="{{ old('closes', $formatDateTime($election->closes)) }}"
                    >
                </div>
            </div>

            <div class="form-entry">
                <span class="description">
                    Poster som kan väljas
                </span>
                <div class="input">
                    @foreach (\App\Models\Position::active() as $position)
                        <div class="checkbox">
                            <input
                                type="checkbox"
                                name="positions[]"
                                value="{{ $position->identifier }}"
                                id="position-{{ $position->identifier }}"
                                @checked(in_array($position->identifier, $selectedPositions))
                            >
                            <label for="position-{{ $position->identifier }}">{{ $position->title }}</label>
                        </div>
                    @endforeach
                    <p>Du kan lägga till fler poster senare.</p>
                </div>
            </div>

            <div class="form-entry">
                <div class="input">
                    <input type="hidden" name="election" value="1">
                    <button type="submit">Lys val</button>
                </div>
            </div>
        </div>
    </form>
@endsection
