@extends('emails.master')

@section('title', $person->kth_username == 'engles' ? 'Du har blivit dominerad!' : 'Du har blivit nominerad')

@section('content')
<p style="margin:0;padding:0;border:0">Hej, {{ $person->name }}!</p>
<br/>
<p style="margin:0;padding:0;border:0">
    Den {{ date("j/n Y", strtotime($election->closes)) }} äger {{ $election->name }} rum och där sker val till ett antal poster på sektionen. Du får detta mail för att du har blivit nominerad till posterna:
    <ul>
        @foreach ($positions as $position)
        <li>{{ $position->title }}</li>
        @endforeach
    </ul>
</p>
<p style="margin:0;padding:0;border:0">
    För att kunna tacka ja till en nominering och sedan bli vald måste du vara sektionsmedlem i Konglig Datasektionen. Om du inte är det kan du enkelt bli medlem genom att betala ett medlemskap till kåren på <a href="http://ths.kth.se">ths.kth.se</a>.
</p>
<br/>
<p style="margin:0;padding:0;border:0">
    <a href="{{ url('/nomination/answer') }}">Du svarar på din nominering genom att trycka på denna länk.</a> Vi ser gärna att du svarar så fort du har bestämt dig för att underlätta i vårt arbete, dock är sista dagen att acceptera nomineringen {{ date("j/n Y",strtotime($election->acceptance_stop)-(86399/2)) }}.
</p>
<br/>
<p style="margin:0;padding:0;border:0">
    På <a href="{{ url('/') }}">valsidan</a> kan du se vilka andra som är nominerade. Se även <a href="http://dfunkt.froyo.datasektionen.se">funktionärssidan</a> för mer utförlig information om posten. Det går även bra att fråga medlemmarna i Valberedningen eller skicka eventuella frågor till <a href="mailto:valberedning@d.kth.se">valberedning@d.kth.se</a>.
</p>
@endsection

@section('ps')
<br/>
<p style="margin:0;padding:0;border:0">
    Om du i fortsättningen inte vill få ett e-postmeddelande när du nomineras klickar du på <a href="{{ url('/user/unsubscribe') }}">denna länk</a>.
</p>
@endsection