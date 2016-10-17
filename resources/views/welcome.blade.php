@extends('master')

@section('title', 'Val')

@section('content')
@forelse(App\Models\Election::open() as $election)
<h1>{{ $election->name }}</h1>
<p>Alla poster som ska väljas visas nedan tillsammans med nominerade personer för den posten.</p>
<ul class="elections">
	@foreach($election->positions as $position)
	<li>
		<h3>{{ $position->name }}</h3>
		@if($position->nominees($election)->get()->count() > 0)
		<p></p>
		<ul>
			@foreach($position->nominees($election)->get() as $nominee)
			<li class="{{ $nominee->pivot->status == 'accepted' ? 'accepted' : ($nominee->pivot->status == 'declined' ? 'declined' : '') }}">
				<div class="crop" style="background-image: url(https://zfinger.datasektionen.se/user/{{ $nominee->kth_username }}/image/100);"></div>
				@if ($nominee->pivot->status == 'accepted')
					Accepterat:
				@elseif ($nominee->pivot->status == 'declined')
					Tackat nej:
				@endif
				<a href="/person/{{ $nominee->id }}">{{ $nominee->name }}</a>
				@if (Auth::check() && Auth::user()->isAdmin())
				<a href="/admin/elections/remove-nomination/{{ $nominee->pivot->uuid }}">Ta bort</a>
				@endif
			</li>
			@endforeach
		</ul>
		@else
		<p>Inga nominerade.</p>
		@endif
	</li>
	@endforeach
</ul>
@empty
	<p>Det finns inga öppna val just nu.</p>
@endforelse
@endsection