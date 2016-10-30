@extends('master')

@section('title', 'Nominera')

@section('head-extra')
<script type="text/javascript">
$(document).ready(function () {
    $('#email').on('input', function () {
        $("#year").show();
    });

    $('#name').autocomplete({
        source: function(request, response) {
            $.ajax({
                type: "GET",
                contentType: "application/json; charset=utf-8",
                url: "https://zfinger.datasektionen.se/users/" + request.term,
                dataType: "json",
                success: function (data) {
                    if (data != null) {
                        response(data.results.slice(0,8));
                    }
                },
                error: function(result) {
                    alert("Error");
                }
            });
        },
        minLength: 3,
        delay: 100,
        select: function(event, ui) {
            $("#email").val(ui.item.uid + "@kth.se");
            $("#name").val(ui.item.cn);
            if (ui.item.year)
                $("#year").val(ui.item.year);
            else 
                $("#year").hide();
            return false;
        },
        focus: function(event, ui) {
            $("#email").val(ui.item.uid + "@kth.se");
            $("#name").val(ui.item.cn);
            if (ui.item.year)
                $("#year").val(ui.item.year);
            else 
                $("#year").hide();
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        console.log(item);
        return $("<li></li>")
            .data("item.autocomplete", item)
            .append('<a><div class="crop" style="background-image:url(https://zfinger.datasektionen.se/user/' + item.uid + '/image/)"></div>'+ item.cn + " (" + item.uid + "@kth.se)</a>") 
            .appendTo(ul);
    };;
});
</script>
<style type="text/css">
    input.ui-autocomplete-loading {
        background-image: url(/images/loading.gif);
        background-size: 30px;
        background-repeat: no-repeat;
        background-position: center right;
    }
</style>
@endsection

@section('content')
@if ($positions->flatten()->count() == 0) 
    <p>Det finns inga öppna val att nominera i.</p>
@else

{!! Form::open(['url' => URL::to(Request::path(), [], true)]) !!}
<div class="form">
    <div class="form-entry">
        <span class="description">
            Vem vill du nominera?<br>
            <span class="desc">Börja skriv ett namn så kommer en lista där du kan välja personer. Om du hellre vill skriva manuellt kan du kolla <a href="https://zfinger.datasektionen.se">Z-finger</a> för att hitta KTH-mejl.</span>
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
            @foreach ($positions as $election)
                @if ($positions->count() > 1)
                    <h4>{{ $election->first()->pivot->name }}</h4>
                @endif
                @foreach ($election as $position)
                    <div class="checkbox">
                        {{ Form::checkbox('election_position[]', $position->pivot->election_id . '_' . $position->identifier, false, array('id' => 'position-' . $position->pivot->election_id . '_' . $position->identifier )) }} 
                        <label for="position-{{ $position->pivot->election_id . '_' . $position->identifier }}">{{ $position->title }}</label>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="form-entry">
        <div class="input">
            {!! Form::submit('Nominera', NULL) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endif
@endsection
