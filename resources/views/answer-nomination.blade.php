@extends('master')

@section('title', 'Svara på nominering')

@section('content')
<p class="center">Du har blivit nominerad till följande poster:</p>
<table class="answer">
    @forelse($positions as $position)
    <tr>
        <td>
            <h3>{{ $position->name }}</h3>
            <p>
                {{ $position->description }}
            </p>
        </td>
        <td>
            @if (App\Models\Election::find($position->pivot->election_id)->acceptsAnswers())
                @if($position->pivot->status === 'waiting')
                    <a href="/nomination/answer/accept/{{ $position->pivot->uuid }}" class="action accept">Acceptera</a>
                    <a href="/nomination/answer/decline/{{ $position->pivot->uuid }}" class="action decline">Tacka nej</a>
                @elseif($position->pivot->status === 'accepted')
                    <p>
                        Du har <b>accepterat</b> denna nominering. 
                        <a href="/nomination/answer/regret/{{ $position->pivot->uuid }}">
                            Ångra dig.
                        </a>
                    </p>
                @else
                    <p>Du har <b>tackat nej</b> till denna nominering. <a href="/nomination/answer/regret/{{ $position->pivot->uuid }}">Ångra dig.</a></p>
                @endif
            @else
                @if($position->pivot->status === 'waiting')
                    <p>Du svarade inte.</p>
                @elseif($position->pivot->status === 'accepted')
                    <p>Du <b>accepterade</b>.</p>
                @else
                    <p>Du <b>tackade nej</b>.</p>
                @endif
            @endif
        </td>
    </tr>
    @empty
        <tr style="border:none"><td>Du har inga nomineringar.</td></tr>
    @endforelse
</table>
@endsection