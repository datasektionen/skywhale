@extends('admin.master')

@section('title', 'Blacklist')

@section('action-button')
    <a href="{{ url('/admin/blacklist/new') }}" class="primary-action">Lägg till ny</a>
@endsection

@section('admin-content')
    <form method="POST"  action="{{ request()->getPathInfo() }}">
        @csrf

        <p>Blacklisten innehåller KTH-användarnamn som inte kan nomineras till poster.</p>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>KTHID</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($blacklist as $blackentry)
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input
                                    type="checkbox"
                                    name="blackentries[]"
                                    value="{{ $blackentry->id }}"
                                    id="u{{ $blackentry->id }}"
                                    @checked(in_array($blackentry->id, old('blackentries', [])))
                                >
                                <label for="u{{ $blackentry->id }}"></label>
                            </div>
                        </td>
                        <td>{{ $blackentry->kth_username }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">Inga poster i blacklisten.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <button type="submit" name="remove">Ta bort markerade</button>
    </form>
@endsection
