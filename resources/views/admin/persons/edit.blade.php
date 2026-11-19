@extends('master')

@section('title', "Ändra person: {$person->name}")

@section('action-button')
    {{-- Note: Destructive actions should ideally use a DELETE form rather than a GET link --}}
    <a class="primary-action" href="{{ url("/admin/persons/remove/{$person->id}") }}">
        Ta bort
    </a>
@endsection

@section('content')
<form method="POST" action="{{ url()->current() }}">
    @csrf
    @method('PUT') {{-- Change to PATCH if your route uses Route::patch() --}}

    <div class="form">
        <div class="form-entry">
            <label for="name" class="description">
                Namn på personen:
            </label>
            <div class="input">
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $person->name) }}"
                    placeholder="Namn"
                    required
                >
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-entry">
            <label for="kth_username" class="description">
                KTH-användarnamn:
            </label>
            <div class="input">
                <input
                    type="text"
                    id="kth_username"
                    name="kth_username"
                    value="{{ old('kth_username', $person->kth_username) }}"
                    placeholder="KTH-användarnamn"
                >
                @error('kth_username')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-entry">
            <label for="year" class="description">
                Årskurs:
            </label>
            <div class="input">
                <input
                    type="text"
                    id="year"
                    name="year"
                    value="{{ old('year', $person->year) }}"
                    placeholder="Ex D13"
                >
                @error('year')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-entry">
            <div class="input">
                <button type="submit">Uppdatera person</button>
            </div>
        </div>
    </div>
</form>
@endsection
