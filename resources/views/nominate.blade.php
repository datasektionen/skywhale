@extends('master')

@section('title', 'Nominera')

@section('head-extra')
<script type="text/javascript">
$(document).ready(function () {
    $('#email').on('input', function () {
        $("#year").show();
    });

    $('#name').autocomplete({
        source: "/api/user/search",
        minLength: 3,
        delay: 100,
        select: function(event, ui) {
            $("#email").val(ui.item.id + "@kth.se");
            $("#name").val(ui.item.name);
            if (ui.item.year)
                $("#year").val(ui.item.year);
            else 
                $("#year").hide();
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $("<li></li>")
            .data("item.autocomplete", item)
            .append('<a><div class="crop" style="background-image:url(https://zfinger.datasektionen.se/user/' + item.id + '/image/)"></div>'+ item.label + "</a>") 
            .appendTo(ul);
    };;
});
</script>
@endsection

@section('content')
@if (\App\Models\Election::nominateableElections()->count() == 0) 
<p>Det finns inga öppna val att nominera i.</p>
@else
{!! Form::open() !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Vem vill du nominera?
        </span>
        <div class="input">
            {!! Form::text('name', NULL, array('placeholder' => 'Namn', 'id' => 'name')) !!}
            {!! Form::text('email', NULL, array('placeholder' => 'KTH-mejladress', 'id' => 'email')) !!}
            {!! Form::text('year', NULL, array('placeholder' => 'Årskurs', 'id' => 'year', 'class' => 'small')) !!}
        </div>
    </div>

    <div class="form-entry">
        <span class="description">
            Till vilka poster?
        </span>
        <div class="input">
            @foreach (\App\Models\Election::positionsForAllNominateableElections() as $position)
            <div class="checkbox">
                {{ Form::checkbox('positions[]', $position->id, false, array('id' => 'position-' . $position->id )) }} 
                <label for="position-{{ $position->id }}">{{ $position->name }}</label>
            </div>
            @endforeach
        </div>
    </div>

    @if (\App\Models\Election::nominateableElections()->count() > 1)
    <div class="form-entry">
        <span class="description">
            Val
        </span>
        <div class="input">
            <div class="select">
                {!! Form::select('election', \App\Models\Election::nominateableElections()->pluck('name', 'id')) !!}
            </div>
            <div class="clear"></div>
        </div>
    </div>
    @elseif (\App\Models\Election::nominateableElections()->count() === 1)
    <p>Du nominerar i valet {{ \App\Models\Election::nominateableElections()->first()->name }}.</p>
    {!! Form::hidden('election', \App\Models\Election::nominateableElections()->first()->id) !!}
    @else
    <p>Du nominerar inte i något val.</p>
    {!! Form::hidden('election', -1) !!}
    @endif

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Nominera', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endif
@endsection