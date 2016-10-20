@extends('master')

@section('content')
	<div class="vertical-menu">
		<h3>Meny</h3>
		<ul>
			<li><a href="/admin/elections">Valtillfällen</a></li>
			<li><a href="/admin/positions">Poster</a></li>
			<li><a href="/admin/persons">Personer</a></li>
		</ul>
	</div>
	<div class="main-content admin">
		@yield('admin-content')
	</div>
	<div class="clear"></div>
@endsection