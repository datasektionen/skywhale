@extends('admin.master')

@section('title', 'Användare som inte får e-post')

@section('admin-content')
{!! Form::open(['url' => URL::to(Request::path(), [], Request::secure())]) !!}
<p>Det här är en lista över personer som blivit tillagda men inte fått något mejl. Om du kan bekräfta att dessa personer är medlemmar i Datasektionen (eller kan bli), så gör det. Om personerna inte är seriösa kandidater, bekräfta inte. Du kan också lägga till dem i blacklisten direkt.</p>
<table>
	<tr>
		<th>KTH-mejl</th>
		<th>Aktivera e-post</th>
		<th>Svartlista</th>
	</tr>
	@foreach ($whitelist as $whiteentry) 
		<tr>
			<td>{{ $whiteentry->email }}</td>
			<td>
				<div class="radio">
					{!! Form::radio('responses[' . $whiteentry->id . ']', "accept", false, ['id' => 'u' . $whiteentry->id]) !!}
					<label for="u{{ $whiteentry->id }}"></label>
				</div>
			</td>
			<td>
				<div class="radio">
					{!! Form::radio('responses[' . $whiteentry->id . ']', "blacklist", false, ['id' => 'a' . $whiteentry->id]) !!}
					<label for="a{{ $whiteentry->id }}"></label>
				</div>
			</td>
		</tr>
	@endforeach
</table>
{!! Form::submit('Genomför ändringar', ['name' => 'submit']) !!}
{!! Form::close() !!}
@endsection
