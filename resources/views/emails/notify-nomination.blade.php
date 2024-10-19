@extends('emails.master')

@section('content')
# Hej, {{ $person->name }}!

Den {{ date("j/n Y", strtotime($election->closes)) }} äger {{ $election->name }} rum och där sker val till ett antal poster på sektionen. Du får detta mail för att du har blivit nominerad till posterna:

@foreach ($positions as $position)
* {{ $position->title }}
@endforeach

För att kunna tacka ja till en nominering och sedan bli vald måste du vara sektionsmedlem i Konglig Datasektionen. Om du inte är det kan du enkelt bli medlem genom att betala ett medlemskap till kåren på [ths.kth.se](http://ths.kth.se).

[Du svarar på din nominering genom att trycka på denna länk.]({{ url('/nomination/answer') }}) Vi ser gärna att du svarar så fort du har bestämt dig för att underlätta i vårt arbete, dock är sista dagen att acceptera nomineringen {{ date("j/n Y",strtotime($election->acceptance_stop)-(86399/2)) }}.

På [valsidan]({{ url('/') }}) kan du se vilka andra som är nominerade. Se även [funktionärssidan](https://dfunkt.datasektionen.se) för mer utförlig information om posten. Det går även bra att fråga medlemmarna i Valberedningen eller skicka eventuella frågor till [valberedning@datasektionen.se](mailto:valberedning@datasektionen.se).
@endsection

@section('ps')
Om du i fortsättningen inte vill få ett e-postmeddelande när du nomineras klickar du på [denna länk]({{ url('/user/unsubscribe') }}).
@endsection
