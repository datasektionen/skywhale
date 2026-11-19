@extends('admin.master')

@section('title', 'Alla personer')

@section('action-button')
    <a href="{{ url('/admin/persons/new') }}" class="primary-action">Ny person</a>
@endsection

@section('head-extra')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll("input[name='users[]']");
            const mergeButton = document.getElementById('mergeButton');

            const checkForUpdates = () => {
                const checkedCount = document.querySelectorAll("input[name='users[]']:checked").length;
                mergeButton.classList.toggle('inactive', checkedCount < 2);
                mergeButton.disabled = checkedCount < 2;
            };

            checkboxes.forEach(cb => cb.addEventListener('change', checkForUpdates));
            checkForUpdates();
        });
    </script>
@endsection

@section('admin-content')
    <form method="POST" action="{{ url('/admin/persons/merge') }}">
        @csrf

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Namn</th>
                    <th>KTH-användarnamn</th>
                    <th>År</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($persons as $person)
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input
                                    type="checkbox"
                                    name="users[]"
                                    value="{{ $person->id }}"
                                    id="person-{{ $person->id }}"
                                    @checked(in_array($person->id, old('users', [])))
                                >
                                <label for="person-{{ $person->id }}"></label>
                            </div>
                        </td>
                        <td>
                            <a href="{{ url("/admin/persons/edit/{$person->id}") }}" title="Ändra">
                                {{ $person->name }}
                            </a>
                        </td>
                        <td>{{ $person->kth_username }}</td>
                        <td>{{ $person->year }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Inga personer hittades.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $persons->links() }}

        <button type="submit" id="mergeButton" class="inactive" disabled>
            Slå samman markerade personer
        </button>
    </form>
@endsection
