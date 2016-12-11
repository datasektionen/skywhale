@extends('master')

@section('title', 'Val')

@section('head-extra')
<script type="text/javascript">
	$(document).ready(function() {
		$('.hidebox-a').change(function () {
			if ($(this).prop('checked')) {
				$('.waiting').hide();
			} else {
				$('.waiting').show();
			}
		});

		$('.hidebox-b').change(function () {
			if ($(this).prop('checked')) {
				$('.declined').hide();
			} else {
				$('.declined').show();
			}
		});

		$('.hidebox-b').prop('checked', 'checked').change();
	});
</script>
@endsection

@section('content')
	@forelse($elections as $election)
		<h1>{{ $election->name }}</h1>

		<div class="clear"></div>
		
		<p>{{ $election->description }}</p>
		
		<p>
			Nomineringsstopp är {{ date("Y-m-d H:i", strtotime($election->nomination_stop)) }}, 
			acceptansstopp är {{ date("Y-m-d H:i", strtotime($election->acceptance_stop)) }} 
			och valet stänger {{ date("Y-m-d H:i", strtotime($election->closes)) }}.
		</p>

		<p>Alla poster som ska väljas visas nedan tillsammans med nominerade personer för den posten.</p>

		<div class="checkbox" style="display: inline-block;width: 300px;">
			{!! Form::checkbox('', '', false, ['class' => 'hidebox-a', 'id' => 'hide-declined-a-'.$election->id]) !!}
			<label for="hide-declined-a-{{ $election->id }}">Göm ej besvarade nomineringar</label>
		</div>
		<div class="checkbox" style="display: inline-block;">
			{!! Form::checkbox('', '', false, ['class' => 'hidebox-b', 'id' => 'hide-declined-b-'.$election->id]) !!}
			<label for="hide-declined-b-{{ $election->id }}">Göm avböjda nomineringar</label>
		</div>

		<ul class="elections" id="election-{{ $election->id }}">
			@foreach($election->positions() as $position)
				<li>
					<h3>{{ $position->title }}</h3>
					<p>
						@if ($position->pivot->nomination_stop !== null)
							Nomineringsstopp är {{ date("Y-m-d H:i", strtotime($position->pivot->nomination_stop)) }}.
						@endif
						@if ($position->pivot->acceptance_stop !== null)
							Acceptansstopp är {{ date("Y-m-d H:i", strtotime($position->pivot->acceptance_stop)) }}.
						@endif
					</p>
					@if($election->nominees($position)->count() > 0)
						<ul>
							@foreach($election->nominees($position) as $nominee)
							<li class="{{ $nominee->status == 'accepted' ? 'accepted' : ($nominee->status == 'declined' ? 'declined' : ($nominee->status == 'accepted' ? 'acccepted' : 'waiting')) }}">
								<div class="crop" style="background-image: url(https://zfinger.datasektionen.se/user/{{ \App\Models\User::find($nominee->user_id)->kth_username }}/image);"></div>
								
								@if ($nominee->status == 'accepted')
									Accepterat:
								@elseif ($nominee->status == 'declined')
									Tackat nej:
								@endif
								
								<a href="/person/{{ $nominee->user_id }}">{{ $nominee->name }}</a>
								
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