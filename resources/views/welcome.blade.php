@extends('master')

@section('title', 'Val')

@section('head-extra')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggles = [
                { id: 'hide-waiting', selector: '.waiting', hideWhenChecked: true },
                { id: 'hide-declined', selector: '.declined', hideWhenChecked: true },
                { id: 'show-images', selector: '.profile-img', hideWhenChecked: false },
            ];

            toggles.forEach(({ id, selector, hideWhenChecked }) => {
                const checkbox = document.getElementById(id);
                if (!checkbox) return;

                const saved = localStorage.getItem(id);
                if (saved !== null) {
                    checkbox.checked = saved === 'true';
                }

                const updateVisibility = () => {
                    const shouldHide = hideWhenChecked ? checkbox.checked : !checkbox.checked;
                    document.querySelectorAll(selector).forEach(el => {
                        el.classList.toggle('hidden', shouldHide);
                    });
                };

                checkbox.addEventListener('change', () => {
                    localStorage.setItem(id, checkbox.checked);
                    updateVisibility();
                });

                updateVisibility();
            });
        });
    </script>
@endsection

@section('content')
    @if($elections->isNotEmpty())
        <div class="filters">
            <div class="checkbox" style="display: inline-block; width: 300px;">
                <input type="checkbox" id="hide-waiting" checked>
                <label for="hide-waiting">Göm ej besvarade nomineringar</label>
            </div>

            <div class="checkbox" style="display: inline-block; width: 300px;">
                <input type="checkbox" id="hide-declined" checked>
                <label for="hide-declined">Göm avböjda nomineringar</label>
            </div>

            <div class="checkbox" style="display: inline-block;">
                <input type="checkbox" id="show-images">
                <label for="show-images">Visa bilder</label>
            </div>
        </div>
    @endif

    @forelse($elections as $election)
        <h1>{{ $election->name }}</h1>

        <div class="clear"></div>

        <p>{!! nl2br(e($election->description)) !!}</p>

        <p>
            Nomineringsstopp är {{ $election->nomination_stop->format('Y-m-d H:i') }},
            acceptansstopp är {{ $election->acceptance_stop->format('Y-m-d H:i') }}
            och valet stänger {{ $election->closes->format('Y-m-d H:i') }}.
        </p>

        <p>Alla poster som ska väljas visas nedan tillsammans med nominerade personer för den posten.</p>

        <ul class="elections" id="election-{{ $election->id }}">
            @foreach($election->positions() as $position)
                <li>
                    <h3>
                        {{ $position->title }}
                        @if($position->pivot->count > 1)
                            <span class="position-count">×{{ $position->pivot->count }}</span>
                        @endif
                    </h3>

                    @if($position->pivot->nomination_stop || $position->pivot->acceptance_stop)
                        <p>
                            @if($position->pivot->nomination_stop)
                                Nomineringsstopp är {{ \Illuminate\Support\Carbon::parse($position->pivot->nomination_stop)->format('Y-m-d H:i') }}.
                            @endif
                            @if($position->pivot->acceptance_stop)
                                Acceptansstopp är {{ \Illuminate\Support\Carbon::parse($position->pivot->acceptance_stop)->format('Y-m-d H:i') }}.
                            @endif
                        </p>
                    @endif

                    @php $nominees = $election->nominees($position); @endphp

                    @if($nominees->isNotEmpty())
                        <ul>
                            @foreach($nominees as $nominee)
                                @php
                                    $statusClass = match($nominee->status) {
                                        'accepted' => 'accepted',
                                        'declined' => 'declined',
                                        default    => 'waiting',
                                    };
                                @endphp

                                <li class="{{ $statusClass }}">
                                    <img
                                        loading="lazy"
                                        class="profile-img hidden"
                                        src="{{ \App\Models\User::picture($nominee->kth_username) }}"
                                        alt="{{ $nominee->name }}"
                                    />

                                    @if($nominee->status === 'accepted')
                                        Accepterat:
                                    @elseif($nominee->status === 'declined')
                                        Tackat nej:
                                    @endif

                                    <a href="{{ url('/person/' . $nominee->kth_username) }}">
                                        {{ $nominee->name }} ({{ $nominee->kth_username }})
                                    </a>

                                    @if(auth()->check() && session('admin') == auth()->id())
                                        <a href="{{ url('/admin/elections/edit-nomination/' . $nominee->uuid) }}">Ändra</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Inga nominerade.</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @empty
        <p>Det finns inga öppna val just nu.</p>
    @endforelse
@endsection
