@extends('master')

@section('title', 'Ta bort nominering')

@section('content')
<p>Är du säker på att du vill ta bort att {{ \App\Models\User::find($nomination->user_id)->name }} är nominerad till {{ App\Models\Position::find($nomination->position_id)->name }} i valet {{ App\Models\Election::find($nomination->election_id)->name }}?</p>

<a href="/admin/elections/remove-nomination-sure/{{ $uuid }}" class="action accept">Ja</a>
<a href="/" class="action">Nej</a>
@endsection