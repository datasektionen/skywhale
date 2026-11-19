@extends('master')

@section('title', 'Ändra poster till val: ' . $election->name)

@section('head-extra')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleStopInput = (checkbox, type) => {
        const input = document.querySelector(`input[name="${type}_stop_${CSS.escape(checkbox.value)}"]`);
        if (input) {
            input.classList.toggle('inactive', checkbox.checked);
        }
    };

    document.querySelectorAll('input[name="nomination_stop_null[]"]').forEach((checkbox) => {
        toggleStopInput(checkbox, 'nomination');
        checkbox.addEventListener('change', () => toggleStopInput(checkbox, 'nomination'));
    });

    document.querySelectorAll('input[name="acceptance_stop_null[]"]').forEach((checkbox) => {
        toggleStopInput(checkbox, 'acceptance');
        checkbox.addEventListener('change', () => toggleStopInput(checkbox, 'acceptance'));
    });
});
</script>
@endsection

@section('content')
    <form method="POST"  action="{{ request()->getPathInfo() }}"">
        @csrf

        <div class="form">
            <table>
                <thead>
                    <tr style="background:#eee">
                        <th>Post</th>
                        <th></th>
                        <th colspan="2" style="text-align: center">Nomineringsstopp</th>
                        <th colspan="2" style="text-align: center">Acceptansstopp</th>
                    </tr>
                    <tr style="background:#eee">
                        <th></th>
                        <th>Antal</th>
                        <th>Datum</th>
                        <th>Standard</th>
                        <th>Datum</th>
                        <th>Standard</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($positions as $position)
                        @php
                            $encodedId = urlencode($position->identifier);

                            $nominationStop = old(
                                "nomination_stop_{$encodedId}",
                                $position->pivot->nomination_stop ? str_replace(' ', 'T', $position->pivot->nomination_stop) : ''
                            );
                            $nominationIsNull = old('nomination_stop_null') !== null
                                ? in_array($encodedId, old('nomination_stop_null', []))
                                : $position->pivot->nomination_stop === null;

                            $acceptanceStop = old(
                                "acceptance_stop_{$encodedId}",
                                $position->pivot->acceptance_stop ? str_replace(' ', 'T', $position->pivot->acceptance_stop) : ''
                            );
                            $acceptanceIsNull = old('acceptance_stop_null') !== null
                                ? in_array($encodedId, old('acceptance_stop_null', []))
                                : $position->pivot->acceptance_stop === null;
                        @endphp
                        <tr>
                            <td>{{ $position->title }}</td>
                            <td>
                                <input
                                    type="number"
                                    name="count_{{ $encodedId }}"
                                    value="{{ old("count_{$encodedId}", $position->pivot->count) }}"
                                    class="position-count"
                                >
                            </td>
                            <td>
                                <input
                                    type="datetime-local"
                                    name="nomination_stop_{{ $encodedId }}"
                                    value="{{ $nominationStop }}"
                                    @class(['inactive' => $nominationIsNull])
                                >
                            </td>
                            <td>
                                <div class="checkbox">
                                    <input
                                        type="checkbox"
                                        name="nomination_stop_null[]"
                                        value="{{ $encodedId }}"
                                        id="n_s_u_{{ $position->identifier }}"
                                        @checked($nominationIsNull)
                                    >
                                    <label for="n_s_u_{{ $position->identifier }}"></label>
                                </div>
                            </td>
                            <td>
                                <input
                                    type="datetime-local"
                                    name="acceptance_stop_{{ $encodedId }}"
                                    value="{{ $acceptanceStop }}"
                                    @class(['inactive' => $acceptanceIsNull])
                                >
                            </td>
                            <td>
                                <div class="checkbox">
                                    <input
                                        type="checkbox"
                                        name="acceptance_stop_null[]"
                                        value="{{ $encodedId }}"
                                        id="a_s_u_{{ $position->identifier }}"
                                        @checked($acceptanceIsNull)
                                    >
                                    <label for="a_s_u_{{ $position->identifier }}"></label>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="form-entry">
                <div class="input">
                    <input type="hidden" name="election" value="{{ $election->id }}">
                    <button type="submit">Uppdatera</button>
                </div>
            </div>
        </div>
    </form>
@endsection
