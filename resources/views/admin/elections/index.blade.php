@extends('admin.master')

@section('title', 'Alla val')

@section('action-button')
	<a href="/admin/elections/new" class="primary-action">Nytt valtillfälle</a>
@endsection

@section('admin-content')
<table>
	<tr>
		<th>Namn</th>
		<th>Antal poster</th>
		<th>Öppnar</th>
		<th>Nomineringsstopp</th>
		<th>Acceptansstopp</th>
		<th>Stänger</th>
	</tr>
	@foreach ($elections as $election)
	<tr>
		<td><a href="/admin/elections/edit/{{ $election->id }}" title="Ändra">{{ $election->name }}</a></td>
		<td>{{ $election->positions()->count() }}</td>
		<td>{{ date("Y-m-d H:i", strtotime($election->opens)) }}</td>
		<td>{{ date("Y-m-d H:i", strtotime($election->nomination_stop)) }}</td>
		<td>{{ date("Y-m-d H:i", strtotime($election->acceptance_stop)) }}</td>
		<td>{{ date("Y-m-d H:i", strtotime($election->closes)) }}</td>
	</tr>
	@endforeach
</table>
{!! $elections->links() !!}
@endsection