@extends('master')

@section('title', 'Ändra nominering')

@section('content')
<p>Du ändrar att <b>{{ $user->name }}</b> är nominerad till <b>{{ $positions[$positionId]->title }}</b> i valet {{ $election->name }}. Just nu är statusen "{{ $nomination->status == 'accepted' ? 'Accepterad' : ($nomination->status == 'declined' ? 'Tackat nej' : 'Inte svarat') }}".</p>

<p><a href="/admin/elections/remove-nomination/{{ $uuid }}">Du kan också ta bort nomineringen genom att klicka här.</a></p>
{!! Form::open(['url' => URL::to(Request::path(), [], Request::secure())]) !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
        	Status:
        </span>
        <div class="input">
        	<div class="select">
            	{!! Form::select('status', ['declined' => 'Tackat nej', 'accepted' => 'Accepterat', 'waiting' => 'Inte svarat'], $nomination->status) !!}
            </div>
        </div>
        <div class="clear"></div>
    </div>
    {!! Form::submit('Spara') !!}
</div>
{!! Form::close() !!}
@endsection
