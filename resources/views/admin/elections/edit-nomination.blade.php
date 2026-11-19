@extends('master')

@section('title', 'Ändra nominering')

@section('content')
    @php
        $statusLabels = [
            'declined' => 'Tackat nej',
            'accepted' => 'Accepterad',
            'waiting'  => 'Inte svarat',
        ];
    @endphp

    <p>
        Du ändrar att <strong>{{ $user->name }}</strong> är nominerad till
        <strong>{{ $positions[$positionId]->title }}</strong> i valet {{ $election->name }}.
        Just nu är statusen "{{ $statusLabels[$nomination->status] ?? 'Inte svarat' }}".
    </p>

    <p>
        <a href="{{ url("/admin/elections/remove-nomination/{$uuid}") }}">
            Du kan också ta bort nomineringen genom att klicka här.
        </a>
    </p>

    <form method="POST"  action="{{ request()->getPathInfo() }}"">
        @csrf

        <div class="form">
            <div class="form-entry">
                <label for="status" class="description">
                    Status:
                </label>
                <div class="input">
                    <div class="select">
                        <select name="status" id="status">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $nomination->status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="clear"></div>
            </div>

            <button type="submit">Spara</button>
        </div>
    </form>
@endsection
