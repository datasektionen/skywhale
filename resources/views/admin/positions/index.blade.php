@extends('admin.master')

@section('title', 'Alla poster')

@section('action-button')
	<a href="/admin/positions/new" class="action">Ny post</a>
@endsection

@section('admin-content')
<table>
	<tr>
		<th>Namn</th>
	</tr>
	@foreach ($positions as $position)
	<tr>
		<td><a href="/admin/positions/edit/{{ $position->id }}" title="Ändra">{{ $position->name }}</a></td>
	</tr>
	@endforeach
</table>
{!! $positions->links() !!}
@endsection