@extends('master')

@section('title', 'Slå ihop personer')

@section('content')
    <p>Du håller på att slå ihop följande personer:</p>
    <table>
        <tr>
            <th>Namn</th>
            <th>KTH-användarnamn</th>
            <th>År</th>
        </tr>
        @foreach ($persons as $person)
        <tr>
            <td><a href="/admin/persons/edit/{{ $person->id }}" title="Ändra">{{ $person->name }}</a></td>
            <td>{{ $person->kth_username }}</td>
            <td>{{ $person->year }}</td>
        </tr>
        @endforeach
    </table>

    <p>Nu måste du bara välja information för den slutliga personen.</p>

    {!! Form::open(array('url' => URL::to('admin/persons/merge-final', [], true))) !!}
        <div class="form">
            <div class="form-entry">
                <span class="description">
                    Namn:
                </span>
                <div class="input">
                    {!! Form::text('name', isset($info['name']) ? $info['name'] : '', array('placeholder' => 'Namn')) !!}
                </div>
            </div>

            <div class="form-entry">
                <span class="description">
                    KTH-mejl
                </span>
                <div class="input">
                    {!! Form::text('kth_username', isset($info['kth_username']) ? $info['kth_username'] . '@kth.se' : '', array('placeholder' => 'xxxxxxxx@kth.se')) !!}
                </div>
            </div>

            <div class="form-entry">
                <span class="description">
                    Årskurs
                </span>
                <div class="input">
                    {!! Form::text('year', isset($info['year']) ? $info['year'] : '', array('placeholder' => 'D-XX')) !!}
                </div>
            </div>

            <div class="form-entry">
                <div class="input">
                    {!! Form::hidden('persons', $personsString) !!}
                    {!! Form::submit('Slå ihop personer', NULL) !!}
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
