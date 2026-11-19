@extends('master')

@section('title', 'Nominera')

@section('head-extra')
<style>
    input.ui-autocomplete-loading {
        background-image: url("{{ asset('images/loading.gif') }}");
        background-size: 30px;
        background-repeat: no-repeat;
        background-position: center right;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const emailInput = document.getElementById('email');
    const yearDisplay = document.getElementById('year');
    const nameDisplay = document.getElementById('name');
    const usernameInput = document.getElementById('kth_username');

    if (emailInput && yearDisplay) {
        emailInput.addEventListener('input', () => {
            yearDisplay.style.display = 'block';
        });
    }

    function autoComplete(ui) {
        usernameInput.value = ui.item.id;
        nameDisplay.textContent = ui.item.name;

        if (ui.item.year) {
            yearDisplay.textContent = ui.item.year;
            yearDisplay.style.display = 'block';
        } else {
            yearDisplay.style.display = 'none';
        }
    }

    // Keeping jQuery UI Autocomplete, modernized with native fetch()
    $(usernameInput).autocomplete({
        source: async (request, response) => {
            try {
                const params = new URLSearchParams({ term: request.term });
                const res = await fetch(`/api/user/search?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

                const data = await res.json();
                if (data) {
                    response(data.slice(0, 8));
                }
            } catch (error) {
                console.error('Fel vid hämtning av användare:', error);
                response([]);
            }
        },
        minLength: 3,
        delay: 500,
        select: (event, ui) => {
            autoComplete(ui);
            return false;
        },
        focus: (event, ui) => {
            autoComplete(ui);
            return false;
        }
    }).data('ui-autocomplete')._renderItem = (ul, item) => {
        return $('<li>')
            .data('item.autocomplete', item)
            .append(`<a><img loading="lazy" class="profile-img" src="${item.picture}" alt="" />${item.label}</a>`)
            .appendTo(ul);
    };
});
</script>
@endsection

@section('content')
    @if ($positions->flatten()->isEmpty())
        <p>Det finns inga öppna val att nominera i.</p>
    @else
        <form method="POST"  action="{{ request()->getPathInfo() }}"">
            @csrf

            <div class="form">
                <div class="form-entry">
                    <span class="description">
                        Sök på personer att nominera?<br>
                        <span class="desc">Börja skriv ett namn så kommer en lista där du kan välja personer.</span>
                    </span>
                    <div class="input">
                        <input
                            type="text"
                            name="kth_username"
                            id="kth_username"
                            value="{{ old('kth_username') }}"
                            placeholder="Name/KTHID"
                            autocomplete="off"
                            required
                        >
                        @error('kth_username')
                            <span class="error">{{ $message }}</span>
                        @enderror
                        <p id="name"></p>
                        <p id="year"></p>
                    </div>
                </div>

                <div class="form-entry">
                    <span class="description">
                        Till vilken post?
                    </span>
                    <div class="input">
                        @foreach ($positions as $election)
                            @if ($positions->count() > 1)
                                <h4>{{ $election->first()->pivot->name }}</h4>
                            @endif

                            @foreach ($election as $position)
                                @php
                                    $value = "{$position->pivot->election_id}_{$position->identifier}";
                                    $inputId = "position-{$value}";
                                @endphp
                                <div class="radio">
                                    <input
                                        type="radio"
                                        name="election_position"
                                        id="{{ $inputId }}"
                                        value="{{ $value }}"
                                        @checked(old('election_position') === $value)
                                        required
                                    >
                                    <label for="{{ $inputId }}">{{ $position->title }}</label>
                                </div>
                            @endforeach
                        @endforeach

                        @error('election_position')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-entry">
                    <div class="input">
                        <button type="submit">Nominera</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection
