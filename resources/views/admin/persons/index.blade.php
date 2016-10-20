@extends('admin.master')


@section('title', 'Alla personer')


@section('action-button')
	<a href="/admin/persons/new" class="action">Ny person</a>
@endsection


@section('head-extra')
	<script type="text/javascript">
		$(document).ready(function () {
			var checkForUpdates = function () {
				if ($("input[type='checkbox']:checked").length >= 2) {
					$('#mergeButton').removeClass('inactive');
				} else {
					$('#mergeButton').addClass('inactive');
				}
			};
			$("input[type='checkbox']").change(checkForUpdates);
			checkForUpdates();
		});
	</script>
@endsection


@section('admin-content')
	{!! Form::open(array('url' => '/admin/persons/merge')) !!}
	<table>
		<tr>
			<th></th>
			<th>Namn</th>
			<th>KTH-användarnamn</th>
			<th>År</th>
		</tr>
		@foreach ($persons as $person)
		<tr>
			<td>
				<div class="checkbox">
					{!! Form::checkbox('users[]', $person->id, false, array('id' => 'person-' . $person->id)) !!}
					<label for="person-{{ $person->id }}"></label>
				</div>
			</td>
			<td><a href="/admin/persons/edit/{{ $person->id }}" title="Ändra">{{ $person->name }}</a></td>
			<td>{{ $person->kth_username }}</td>
			<td>{{ $person->year }}</td>
		</tr>
		@endforeach
	</table>
	{!! $persons->links() !!}
	{!! Form::submit('Slå samman markerade personer', array('class' => 'inactive', 'id' => 'mergeButton')) !!}
	{!! Form::close() !!}
@endsection