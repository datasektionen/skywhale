@extends('admin.master')

@section('title', 'Påminnelsemejl')

@section('admin-content')
{!! Form::open(['url' => URL::to(Request::path(), [], Request::secure())]) !!}
<p>Personer som inte har svarat på minst en aktuell nominering visas i tabellen nedan. Du kan bara påminna personer som du inte påmint inom de 24 senaste timmarna.</p>
<table>
	<tr>
		<th></th>
		<th>Namn</th>
		<th>Poster</th>
		<th>Senast påmind</th>
	</tr>
	@foreach ($nominees as $nominee) 
		<tr>
			<td>
				<div class="checkbox">
					{!! Form::checkbox('users[]', $nominee[0]->id, true, ['id' => 'u' . $nominee[0]->id]) !!}
					<label for="u{{ $nominee[0]->id }}"></label>
				</div>
			</td>
			<td>{{ $nominee[0]->name }}</td>
			<td>
				{{ count($nominee) }}:
				@foreach ($nominee as $position)
					{{ $position->election_name }}: {{ $position->position }},
				@endforeach
			</td>
			<td>
				{{ $nominee[0]->reminded === null ? 'Aldrig' : date("Y-m-d H:i", strtotime($nominee[0]->reminded)) }}
			</td>
		</tr>
	@endforeach
</table>
{!! Form::submit('Skicka påminnelsemejl till markerade') !!}
{!! Form::close() !!}
@endsection
