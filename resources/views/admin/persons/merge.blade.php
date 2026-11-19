@extends('master')

@section('title', 'Slå ihop personer')

@section('content')
    <p>Du håller på att slå ihop följande personer:</p>

    <table>
        <thead>
            <tr>
                <th>Namn</th>
                <th>KTH-användarnamn</th>
                <th>År</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($persons as $person)
                <tr>
                    <td>
                        <a href="{{ url("/admin/persons/edit/{$person->id}") }}" title="Ändra">
                            {{ $person->name }}
                        </a>
                    </td>
                    <td>{{ $person->kth_username }}</td>
                    <td>{{ $person->year }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Nu måste du bara välja information för den slutliga personen.</p>

    <form method="POST" action="{{ url('/admin/persons/merge-final') }}">
        @csrf

        <div class="form">
            <div class="form-entry">
                <label for="name" class="description">
                    Namn:
                </label>
                <div class="input">
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $info['name'] ?? '') }}"
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
                    KTH-mejl
                </label>
                <div class="input">
                    <input
                        type="email"
                        id="kth_username"
                        name="kth_username"
                        value="{{ old('kth_username', filled($info['kth_username'] ?? null) ? "{$info['kth_username']}@kth.se" : '') }}"
                        placeholder="xxxxxxxx@kth.se"
                    >
                    @error('kth_username')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-entry">
                <label for="year" class="description">
                    Årskurs
                </label>
                <div class="input">
                    <input
                        type="text"
                        id="year"
                        name="year"
                        value="{{ old('year', $info['year'] ?? '') }}"
                        placeholder="D-XX"
                    >
                    @error('year')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-entry">
                <div class="input">
                    <input type="hidden" name="persons" value="{{ old('persons', $personsString) }}">
                    <button type="submit">Slå ihop personer</button>
                </div>
            </div>
        </div>
    </form>
@endsection
