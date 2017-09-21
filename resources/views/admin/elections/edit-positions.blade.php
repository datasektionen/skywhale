@extends('master')

@section('title', 'Ändra poster till val: ' . $election->name)

@section('head-extra')
<script type="text/javascript">
$(document).ready(function () {
    $('input[name="nomination_stop_null[]"]').on('change', function() {
        check(this, 'nomination');
    });
    $('input[name="nomination_stop_null[]"]').each(function() {
        check(this, 'nomination');
    });
    $('input[name="acceptance_stop_null[]"]').on('change', function() {
        check(this, 'acceptance');
    });
    $('input[name="acceptance_stop_null[]"]').each(function() {
        check(this, 'acceptance');
    });

    function check(elem, type) {
        if (!$(elem).prop('checked'))
            $('input[name="' + type + '_stop_' + $(elem).val() + '"]').removeClass('inactive'); 
        else
            $('input[name="' + type + '_stop_' + $(elem).val() + '"]').addClass('inactive'); 
    }
});
</script>
@endsection

@section('content')
{!! Form::open(['url' => URL::to(Request::path(), [], true)]) !!}
<div class="form">
    <table>
        <tr style='background:#eee'>
            <th>Post</th>
            <th colspan="2" style="text-align: center">Nomineringsstopp</th>
            <th colspan="2" style="text-align: center">Acceptansstopp</th>
        </tr>
        <tr style='background:#eee'>
            <th></th>
            <th>Datum</th>
            <th>Standard</th>
            <th>Datum</th>
            <th>Standard</th>
        </tr>
        @foreach ($positions as $position)
        <tr>
            <td>{{ $position->title }}</td>
            <td>
                {!! Form::input('datetime-local', 'nomination_stop_' . $position->identifier, str_replace(" ", "T", $position->pivot->nomination_stop), ['class' => $position->pivot->nomination_stop === null ? 'inactive' : '']) !!}
            </td>
            <td>
                <div class="checkbox">
                    {!! Form::checkbox('nomination_stop_null[]', $position->identifier, $position->pivot->nomination_stop === null, ['id' => 'n_s_u_' . $position->identifier ]) !!}
                    <label for="n_s_u_{{ $position->identifier }}"></label>
                </div>
            </td>
            <td>
                {!! Form::input('datetime-local', 'acceptance_stop_' . $position->identifier, str_replace(" ", "T", $position->pivot->acceptance_stop), ['class' => $position->pivot->acceptance_stop === null ? 'inactive' : '']) !!}
            </td>
            <td>
                <div class="checkbox">
                    {!! Form::checkbox('acceptance_stop_null[]', $position->identifier, $position->pivot->acceptance_stop === null, ['id' => 'a_s_u_' . $position->identifier ]) !!}
                    <label for="a_s_u_{{ $position->identifier }}"></label>
                </div>
            </td>
        </tr>
        @endforeach
    </table>
    <div class="form-entry">
        <div class="input">
            {!! Form::hidden('election', $election->id) !!}
            {!! Form::submit('Updatera', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection
