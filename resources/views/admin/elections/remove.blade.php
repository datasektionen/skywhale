@extends('master')

@section('title', 'Ta bort valtillfälle')

@section('content')
<p>Är du säker på att du vill ta bort {{ $election->name }}? Alla data, såsom poster förknippade med valet, nomineringar och metadata kommer tas bort.</p>

<a href="/admin/elections/remove-confirmed/{{ $election->id }}" class="action accept">Ja</a>
<a href="/admin/elections/edit/{{ $election->id }}" class="action">Nej</a>
@endsection