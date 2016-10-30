@extends('master')

@section('content')
<div class="row">
	<div class="col-sm-4 col-md-3">
		<div id="secondary-nav">
			<h3>Meny</h3>
			<ul>
				<li><a href="/admin/elections">Valtillfällen</a></li>
				<li><a href="/admin/positions">Poster</a></li>
				<li><a href="/admin/persons">Personer</a></li>
				<li><a href="/admin/reminders">Påminnelsemejl</a></li>
			</ul>
		</div>
	</div>
	<div class="col-sm-8 col-md-9">
		@yield('admin-content')
	</div>
	<div class="clear"></div>
</div>
@endsection