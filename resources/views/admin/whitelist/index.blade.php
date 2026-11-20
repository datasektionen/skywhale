@extends('admin.master')

@section('title', 'Användare som inte får e-post')

@section('admin-content')
    <form method="POST"  action="{{ request()->getPathInfo() }}">
        @csrf

        <p>Det här är en lista över personer som blivit tillagda men inte fått något mejl. Om du kan bekräfta att dessa personer är medlemmar i Datasektionen (eller kan bli), så gör det. Om personerna inte är seriösa kandidater, bekräfta inte. Du kan också lägga till dem i blacklisten direkt.</p>

        <table>
            <thead>
                <tr>
                    <th>KTH-mejl</th>
                    <th>Aktivera e-post</th>
                    <th>Svartlista</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($whitelist as $whiteentry)
                    <tr>
                        <td>{{ $whiteentry->email }}</td>
                        <td>
                            <div class="radio">
                                <input
                                    type="radio"
                                    name="responses[{{ $whiteentry->id }}]"
                                    value="accept"
                                    id="u{{ $whiteentry->id }}"
                                    @checked(old("responses.{$whiteentry->id}") === 'accept')
                                >
                                <label for="u{{ $whiteentry->id }}"></label>
                            </div>
                        </td>
                        <td>
                            <div class="radio">
                                <input
                                    type="radio"
                                    name="responses[{{ $whiteentry->id }}]"
                                    value="blacklist"
                                    id="a{{ $whiteentry->id }}"
                                    @checked(old("responses.{$whiteentry->id}") === 'blacklist')
                                >
                                <label for="a{{ $whiteentry->id }}"></label>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Det finns inga ny användare.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <button type="submit" name="submit">Genomför ändringar</button>
    </form>
@endsection
