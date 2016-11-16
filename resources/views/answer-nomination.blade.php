@extends('master')

@section('title', 'Svara på nominering')

@section('content')
@if (count($positions) > 0)
<p class="center">Du har blivit nominerad till följande poster:</p>
@endif
<table class="answer">
    @forelse($positions as $position)
    <tr>
        <td>
            <h3>{{ $position->positionObject->title }} vid {{ $position->electionObject->name }}</h3>
            <p>
                {{ $position->positionObject->description }}
            </p>
        </td>
        <td>
            @if ($position->electionObject->acceptsAnswers($position->uuid))
                @if($position->status === 'waiting')
                    <a href="/nomination/answer/accept/{{ $position->uuid }}" class="action accept">Acceptera</a>
                    <a href="/nomination/answer/decline/{{ $position->uuid }}" class="action decline">Tacka nej</a>
                @elseif($position->status === 'accepted')
                    <p>
                        Du har <b>accepterat</b> denna nominering. 
                        <a href="/nomination/answer/regret/{{ $position->uuid }}">
                            Ångra dig.
                        </a>
                    </p>
                @else
                    <p>Du har <b>tackat nej</b> till denna nominering. <a href="/nomination/answer/regret/{{ $position->uuid }}">Ångra dig.</a></p>
                @endif
            @else
                @if($position->status === 'waiting')
                    <p>Du svarade inte.</p>
                @elseif($position->status === 'accepted')
                    <p>
                        Du <b>accepterade</b>. 
                        <a href="/nomination/answer/regret/{{ $position->uuid }}">
                            Ångra dig. (Du kommer inte kunna acceptera igen om du klickar här.)
                        </a>
                    </p>
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