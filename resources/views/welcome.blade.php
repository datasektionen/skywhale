@extends('master')

@section('title', 'Val')

@section('content')

	@forelse(App\Models\Election::open() as $election)
		<h1>{{ $election->name }}</h1>
		<p>{{ $election->description }}</p>
		<p>Nomineringsstopp är {{ date("Y-m-d H:i", strtotime($election->nomination_stop)) }}, acceptansstopp är {{ date("Y-m-d H:i", strtotime($election->acceptance_stop)) }} och valet stänger {{ date("Y-m-d H:i", strtotime($election->closes)) }}.</p>
		<p>Alla poster som ska väljas visas nedan tillsammans med nominerade personer för den posten.</p>
		<ul class="elections">
			@foreach($election->positions() as $position)
				<li>
					<h3>{{ $position->title }}</h3>
					@if($election->nominees($position)->get()->count() > 0)
						<p></p>
						<ul>
							@foreach($election->nominees($position)->get() as $nominee)
							<li class="{{ $nominee->status == 'accepted' ? 'accepted' : ($nominee->status == 'declined' ? 'declined' : '') }}">
								<div class="crop" style="background-image: url(https://zfinger.datasektionen.se/user/{{ \App\Models\User::find($nominee->user_id)->kth_username }}/image/100);"></div>
								
								@if ($nominee->status == 'accepted')
									Accepterat:
								@elseif ($nominee->status == 'declined')
									Tackat nej:
								@endif
								
								<a href="/person/{{ $nominee->user_id }}">{{ \App\Models\User::find($nominee->user_id)->name }}</a>
								
								@if (Auth::check() && Auth::user()->isAdmin())
									<a href="/admin/elections/edit-nomination/{{ $nominee->uuid }}">Ändra</a>
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