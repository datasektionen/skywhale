@extends('admin.master')

@section('title', 'Alla poster')

@section('admin-content')
<table>
	<tr>
		<th>ID</th>
		<th>Namn</th>
		<th>E-postadress</th>
	</tr>
	@foreach ($positions as $position)
	<tr>
		<td>{{ $position->identifier }}</td><td>{{ $position->title }}</td><td>{{ $position->email }}</td>
	</tr>
	@endforeach
</table>
{!! $positions->links() !!}
@endsection