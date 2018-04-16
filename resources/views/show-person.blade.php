@extends('master')

@section('title', 'Visa person: ' . $user->name)

@section('content')

<div class="center">
	<div class="crop big" style="width: 200px; height: 200px;background-image: url(https://zfinger.datasektionen.se/user/{{ $user->kth_username }}/image);"></div>
	<p>Trött på bilden här ovanför? Byt på <a href="https://zfinger.datasektionen.se?q={{ $user->kth_username }}">Z-finger</a>.</p>
	<table>
		<tr>
			<td>
				Namn:
			</td>
			<td>
				{{ $user->name }}
			</td>
		</tr>
		<tr>
			<td>
				E-postadress:
			</td>
			<td>
				{{ $user->kth_username }}@kth.se
			</td>
		</tr>
		@if (Auth::check() && Auth::user()->isAdmin())
			<tr>
				<td>
					Administrera:
				</td>
				<td>
					<a href="/admin/persons/edit/{{ $user->id }}">Ändra</a> | <a href="/admin/persons/remove/{{ $user->id }}">Ta bort</a>
				</td>
			</tr>
		@endif
	</table>

	<h2 style="text-align: left;">Tidigare poster</h2>
	<table style="width: 100%;">
		<thead>
			<tr>
				<th>
					Post
				</th>
				<th>
					Från
				</th>
				<th>
					Till
				</th>
			</tr>
		</thead>
		@forelse ($roles->mandates as $mandate) 
		<tr>
			<td>
				{{ $mandate->Role->title }}
			</td>
			<td>
				{{ date("Y-m-d", strtotime($mandate->start)) }}
			</td>
			<td>
				{{ date("Y-m-d", strtotime($mandate->end)) }}
			</td>
		</tr>
		@empty
		<tr>
			<td colspan="3">Inga tidigare poster</td>
		</tr>
		@endforelse
	</table>
	<div style="text-align: right;margin: 10px 0;">Data hämtad från <a href="//dfunkt.datasektionen.se/user/{{ $user->kth_username }}" style="color: #9c27b0;">Dfunkt</a>.</div>
</div>
@endsection