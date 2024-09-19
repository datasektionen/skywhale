<?php echo "<?"; ?>xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">

<channel>
  <title>Datasektionens Valsystem - Skywhale</title>
  <link>{{ url("/") }}</link>
  <description>Följ samtliga val på Datasektionen</description>
  @foreach ($events as $event)


  @if ($event->action == "nominated" && $event->user != null)

    <item>
      <title>Ny nominering</title>
      <description>{{ $event->user->name }} nominerades till {{ $positions[$event->position]->title }} i valet {{ $event->election->name }}. </description>
      <author>valberedning@datasektionen.se</author>
      <category>Nominering</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  @elseif ($event->action == "accepted" && $event->user != null)

    <item>
      <title>Nominering accepterad</title>
      <description>{{ $event->user->name }} accepterade sin nominering till posten {{ $positions[$event->position]->title }} i valet {{ $event->election->name }}. </description>
      <author>valberedning@datasektionen.se</author>
      <category>Svar på nominering</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  @elseif ($event->action == "declined" && $event->user != null)

    <item>
      <title>Nominering nekad</title>
      <description>{{ $event->user->name }} tackade nej till sin nominering till posten {{ $positions[$event->position]->title }} i valet {{ $event->election->name }}. </description>
      <author>valberedning@datasektionen.se</author>
      <category>Svar på nominering</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  @elseif ($event->action == "regretted" && $event->user != null)

    <item>
      <title>Svar ångrat</title>
      <description>{{ $event->user->name }} ångrade sitt svar på sin nominering till posten {{ $positions[$event->position]->title }} i valet {{ $event->election->name }}. </description>
      <author>valberedning@datasektionen.se</author>
      <category>Svar på nominering</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  // Following events are currently not in use
  
  @elseif ($event->action == "opened")

    <item>
      <title>Valet {{ $event->election->name }} öppnade</title>
      <link>{{ url("/") }}?{{ $event->id }}</link>
      <description>Valet {{ $event->election->name }} öppnade.</description>
      <author>valberedning@datasektionen.se</author>
      <category>Valinformation</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  @elseif ($event->action == "closed")

    <item>
      <title>Valet {{ $event->election->name }} stängde</title>
      <link>{{ url("/") }}?{{ $event->id }}</link>
      <description>Valet {{ $event->election->name }} stängde.</description>
      <author>valberedning@datasektionen.se</author>
      <category>Valinformation</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  @elseif ($event->action == "acceptance_stopped")

    <item>
      <title>Valet {{ $event->election->name }} nådde acceptansstopp</title>
      <link>{{ url("/") }}?{{ $event->id }}</link>
      <description>Valet {{ $event->election->name }} nådde acceptansstopp.</description>
      <author>valberedning@datasektionen.se</author>
      <category>Valinformation</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  @elseif ($event->action == "nomination_stopped")

    <item>
      <title>Valet {{ $event->election->name }} nådde nomineringsstopp</title>
      <link>{{ url("/") }}?{{ $event->id }}</link>
      <description>Valet {{ $event->election->name }} nådde nomineringsstopp.</description>
      <author>valberedning@datasektionen.se</author>
      <category>Valinformation</category>
      <pubDate>{{ $event->created_at }}</pubDate>
    </item>

  @endif

  @endforeach
</channel>

</rss>
