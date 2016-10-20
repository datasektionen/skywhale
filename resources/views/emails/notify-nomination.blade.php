<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Du har blivit {{ $person->kth_username === "bengles" ? 'dominerad' : 'nominerad' }}!</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <style>@import url(https://fonts.googleapis.com/css?family=Lato:400,300,700,400italic,700italic,900);
            * {
                font-family: "Lato", sans-serif;
                line-height: 1.4;
            }
            a {
                color: #f0c;
            }
        </style>
    </head>
    <body>
        <div class="outer" style="background-color:#F7F7F7;margin:0;padding:0;border:0">
            <div class="main" style="max-width: 700px;margin:0 auto;padding:0;border:0">
                <div class="top" style="background-color:#EE2A7B;margin:0;padding:0;border:0;text-align:center;height:10px;">
                </div>
                <div class="content" style="background-color:#FFF;padding: 30px 30px;margin:0;border:0">
                    <p style="margin:0;padding:0;border:0">Hej, {{ $person->name }}!</p>
                    <br/>
                    <p style="margin:0;padding:0;border:0"> Den {{ date("j/n Y", strtotime($election->closes)) }} äger {{ $election->name }} rum och där sker val till ett antal poster på sektionen. Du får detta mail för att du har blivit nominerad till posterna:
                    <ul>
                        @foreach ($positions as $position)
                            <li>{{ $position->title }}</li>
                        @endforeach
                    </ul>
                    <p style="margin:0;padding:0;border:0"> För att kunna tacka ja till en nominering och sedan bli vald måste du vara sektionsmedlem i Konglig Datasektionen. Om du inte är det kan du enkelt bli medlem genom att betala ett medlemskap till kåren på <a href="//ths.kth.se">ths.kth.se</a>.</p>
                    <br/>
                    <p style="margin:0;padding:0;border:0"><a href="{{ url('/nomination/answer') }}">Du svarar på din nominering genom att trycka på denna länk.</a> Vi ser gärna att du svarar så fort du har bestämt dig för att underlätta i vårt arbete, dock är sista dagen att acceptera nomineringen {{ date("j/n Y",strtotime($election->acceptance_stop)-86399) }}. </p>
                    <br/>
                    <p style="margin:0;padding:0;border:0"> På <a href="{{ url('/') }}">valsidan</a> kan du se vilka andra som är nominerade. Se även funktionärssidan för mer utförlig information om posten. Det går även bra att fråga medlemmarna i Valberedningen eller skicka eventuella frågor till <a href="mailto:valberedning@d.kth.se">valberedning@d.kth.se</a>.</p>
                    <br/>
                    <p style="margin:0;padding:0;border:0"> Med vänliga hälsningar,</p>
                    <br/>
                    <p style="margin:0;padding:0;border:0">Valberedningen</p>
                </div>
                <div class="footer" style="background-color:#EE2A7B;margin:0;padding:0;border:0;text-align:center;">
                    <img src="https://www.datasektionen.se/static/gfx/Skold_Vit_Final.png" style="height:100px;width:100px;margin:30px auto 0;text-align:center;"/>
                    <h1 style="color:#FFF;text-align:center;font-size:30px;height:30px;padding:0px 0 29px 0;-webkit-margin-before: 0px; -webkit-margin-after: 0px;margin:0;border:0">Konglig Datasektionen</h1>
                </div>
            </div>
        </div>
    </body>
</html>