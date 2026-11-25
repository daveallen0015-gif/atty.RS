@if ($laws->isNotEmpty())
    <div class="law-list">@foreach ($laws as $law)
        <article class="law-entry">
            <div class="law-entry-meta"><span>{{ $law->category->category_name }}</span>@if($law->date_approved)<time datetime="{{ $law->date_approved->format('Y-m-d') }}">{{ $law->date_approved->format('F j, Y') }}</time>@endif</div>
            <h3>{{ $law->official_title }}</h3>
            <p class="law-entry-number">{{ $law->law_type }} No. {{ $law->law_number }}</p>
            @if ($law->short_description)<p class="law-entry-description">{{ $law->short_description }}</p>@endif
            <div class="law-entry-links">
                <a href="{{ route('laws.show', $law) }}">View law details <span aria-hidden="true">&rarr;</span></a>
                @if ($law->official_source_url)<a href="{{ $law->official_source_url }}" target="_blank" rel="noopener noreferrer">Open source <span aria-hidden="true">&#8599;</span></a>@endif
            </div>
        </article>
    @endforeach</div>
    {{ $laws->links('pagination::bootstrap-5') }}
@else
    <p class="law-empty-state">No Philippine laws matched your search. Try another title, number, or category.</p>
@endif
