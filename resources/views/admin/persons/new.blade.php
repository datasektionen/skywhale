@extends('master')

@section('title', 'Skapa ny person')

@section('content')
    <form method="POST" action="{{ url()->current() }}">
        @csrf

        <div class="form">
            <div class="form-entry">
                <label for="name" class="description">Namn:</label>
                <div class="input">
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="Namn"
                    >
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-entry">
                <label for="kth_username" class="description">KTH-mejl</label>
                <div class="input">
                    <input
                        type="email"
                        name="kth_username"
                        id="kth_username"
                        value="{{ old('kth_username') }}"
                        placeholder="xxxxxxxx@kth.se"
                    >
                    @error('kth_username')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-entry">
                <label for="year" class="description">Årskurs</label>
                <div class="input">
                    <input
                        type="text"
                        name="year"
                        id="year"
                        value="{{ old('year') }}"
                        placeholder="DXX"
                    >
                    @error('year')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-entry">
                <div class="input">
                    <button type="submit">Skapa person</button>
                </div>
            </div>
        </div>
    </form>
@endsection
