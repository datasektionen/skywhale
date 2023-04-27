@extends('admin.master')

@section('title', 'Blacklist')

@section('action-button')
	<a href="/admin/blacklist/new" class="primary-action">Lägg till ny</a>
@endsection

@section('admin-content')
{!! Form::open(['url' => URL::to(Request::path(), [], Request::secure())]) !!}
<p>Blacklisten innehåller KTH-användarnamn som inte kan nomineras till poster.</p>
<table>
	<tr>
		<th></th>
		<th>KTH-mejl</th>
	</tr>
	@foreach ($blacklist as $blackentry) 
		<tr>
			<td>
				<div class="checkbox">
					{!! Form::checkbox('blackentries[]', $blackentry->id, false, ['id' => 'u' . $blackentry->id]) !!}
					<label for="u{{ $blackentry->id }}"></label>
				</div>
			</td>
			<td>{{ $blackentry->kth_username }}@kth.se</td>
		</tr>
	@endforeach
</table>
{!! Form::submit('Ta bort markerade', ['name' => 'remove']) !!}
{!! Form::close() !!}
@endsection
