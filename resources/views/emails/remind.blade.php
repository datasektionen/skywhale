@extends('emails.master')

@section('title', $person->kth_username == 'engles' ? 'Du har poster att dominera!' : 'Du har poster att acceptera!')

@section('content')
<p style="margin:0;padding:0;border:0">Hej, {{ $person->name }}!</p>
<br/>
<p style="margin:0;padding:0;border:0">
    Den {{ date("j/n Y", strtotime($election->closes)) }} äger {{ $election->name }} rum och där sker val till ett antal poster på sektionen. Du får detta mejl för att du ännu inte svarat på dina nomineringar till posterna:
    <ul>
        @foreach ($positions as $position)
        <li>{{ $position->title }}</li>
        @endforeach
    </ul>
</p>
<p style="margin:0;padding:0;border:0">
    Vi skulle nu vilja att du <a href="{{ url('/nomination/answer') }}">besöker valsidan och svarar på dina nomineringar</a>. Länken är <a href="{{ url('/nomination/answer') }}">{{ url('/nomination/answer') }}</a> om din e-postklient inte klarar av att visa länkar. Sista dagen att acceptera dina nomineringar är {{ date("j/n Y",strtotime($election->acceptance_stop)-(86399/2)) }}, om inget speciellt gäller för de poster du blivit nominerad till.
</p>
<br/>
<p style="margin:0;padding:0;border:0">
    För att kunna tacka ja till en nominering och sedan bli vald måste du vara sektionsmedlem i Konglig Datasektionen. Om du inte är det kan du enkelt bli medlem genom att betala ett medlemskap till kåren på <a href="//ths.kth.se">ths.kth.se</a>.
</p>
<br/>
<p style="margin:0;padding:0;border:0">
    På <a href="{{ url('/') }}">valsidan</a> kan du se vilka andra som är nominerade. Se även <a href="http://dfunkt.froyo.datasektionen.se">funktionärssidan</a> för mer utförlig information om posten. Det går även bra att fråga medlemmarna i Valberedningen eller skicka eventuella frågor till <a href="mailto:valberedning@d.kth.se">valberedning@d.kth.se</a>.
</p>
@endsection