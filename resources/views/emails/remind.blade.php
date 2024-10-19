@extends('emails.master')

@section('content')
# Hej, {{ $person->name }}!

Den {{ date("j/n Y", strtotime($election->closes)) }} äger {{ $election->name }} rum och där sker val till ett antal poster på sektionen. Du får detta mail för att du har blivit nominerad till posterna:

@foreach ($positions as $position)
* {{ $position->title }}
@endforeach

Vi skulle nu vilja att du [besöker valsidan]({{ url('/nomination/answer') }}) och svarar på dina nomineringar. Länken är [{{ url('/nomination/answer') }}]({{ url('/nomination/answer') }}) om din e-postklient inte klarar av att visa länkar. Sista dagen att acceptera dina nomineringar är {{ date("j/n Y",strtotime($election->acceptance_stop)-(86399/2)) }}, om inget speciellt gäller för de poster du blivit nominerad till.

För att kunna tacka ja till en nominering och sedan bli vald måste du vara sektionsmedlem i Konglig Datasektionen. Om du inte är det kan du enkelt bli medlem genom att betala ett medlemskap till kåren på [ths.kth.se](//ths.kth.se).

På [valsidan]({{ url('/') }}) kan du se vilka andra som är nominerade. Se även [funktionärssidan](https://dfunkt.datasektionen.se) för mer utförlig information om posten. Det går även bra att fråga medlemmarna i Valberedningen eller skicka eventuella frågor till [valberedning@datasektionen.se](mailto:valberedning@datasektionen.se).
@endsection
