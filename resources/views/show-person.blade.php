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
</div>
@endsection