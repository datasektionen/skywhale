@extends('master')

@section('title', 'Visa person: ' . $user->name)

@section('content')

<div class="crop big" style="width: 200px; height: 200px;background-size: 200px;background-image: url(https://zfinger.datasektionen.se/user/{{ $user->kth_username }}/image/500);"></div>
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
</table>
@endsection