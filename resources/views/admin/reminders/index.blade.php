@extends('admin.master')

@section('title', 'Påminnelsemejl')

@section('admin-content')
    <form method="POST"  action="{{ request()->getPathInfo() }}"">
        @csrf

        <p>Personer som inte har svarat på minst en aktuell nominering visas i tabellen nedan. Du kan bara påminna personer som du inte påmint inom de 24 senaste timmarna.</p>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Namn</th>
                    <th>Poster</th>
                    <th>Senast påmind</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($nominees as $nominee)
                    @php
                        $person = $nominee[0];
                        $isChecked = old('_token')
                            ? in_array($person->id, old('users', []))
                            : true;
                    @endphp
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input
                                    type="checkbox"
                                    name="users[]"
                                    value="{{ $person->id }}"
                                    id="u{{ $person->id }}"
                                    @checked($isChecked)
                                >
                                <label for="u{{ $person->id }}"></label>
                            </div>
                        </td>
                        <td>{{ $person->name }}</td>
                        <td>
                            {{ count($nominee) }}:
                            {{ collect($nominee)->map(fn ($p) => "{$p->election_name}: {$p->position}")->join(', ') }}
                        </td>
                        <td>
                            {{ $person->reminded ? \Illuminate\Support\Carbon::parse($person->reminded)->format('Y-m-d H:i') : 'Aldrig' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Inga personer att påminna just nu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <button type="submit">Skicka påminnelsemejl till markerade</button>
    </form>
@endsection
