@extends('master')

@section('title', 'Ta bort person')

@section('content')
<p>Är du säker på att du vill ta bort att {{ $person->name }} (KTH-mejl: {{ $person->kth_username }}@kth.se, årskurs: {{ $person->year }})? Detta kommer också ta bort alla nomineringar som personen också haft i alla val någonsin.</p>

<a href="/admin/persons/remove-confirmed/{{ $person->id }}" class="action accept">Ja</a>
<a href="/admin/persons" class="action">Nej</a>
@endsection