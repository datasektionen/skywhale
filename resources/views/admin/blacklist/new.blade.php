@extends('master')

@section('title', 'Skapa ny person')

@section('content')
    <form method="POST"  action="{{ request()->getPathInfo() }}"">
        @csrf

        <div class="form">
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
                <div class="input">
                    <button type="submit">Blacklista e-post</button>
                </div>
            </div>
        </div>
    </form>
@endsection
