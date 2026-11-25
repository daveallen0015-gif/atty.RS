@extends('public.layouts.app')

@section('title', $law->official_title)

@section('content')
    <article class="law-detail-page">
        <a class="law-back-link" href="{{ route('laws.index', ['category' => $law->category->slug]) }}">&larr; Back to Philippine Laws</a>
        <header class="law-detail-heading">
            <p class="public-eyebrow">{{ $law->category->category_name }}</p>
            <p class="law-entry-number">{{ $law->law_type }} No. {{ $law->law_number }}</p>
            <h1>{{ $law->official_title }}</h1>
            @if ($law->date_approved)<p class="law-approved">Approved {{ $law->date_approved->format('F j, Y') }}</p>@endif
        </header>
        @if ($law->short_description)<section class="law-detail-summary"><h2>About this law</h2><p>{{ $law->short_description }}</p></section>@endif
        <section class="law-full-text">
            <h2>Full text or selected provisions</h2>
            @if ($law->full_text)
                <div class="law-provisions">{{ $law->full_text }}</div>
            @else
                <p>The text of this law has not been added to this library. Please use the source link below to read the original legal document.</p>
            @endif
            @if ($law->document_path)<a class="law-source-button" href="{{ str_starts_with($law->document_path, 'http') ? $law->document_path : asset('storage/'.$law->document_path) }}" target="_blank" rel="noopener noreferrer">View uploaded legal document</a>@endif
            @if ($law->official_source_url)<a class="law-source-button" href="{{ $law->official_source_url }}" target="_blank" rel="noopener noreferrer">Open source legal document ↗</a>@endif
        </section>
        @if ($law->legal_status)<p class="law-status-note">Status / amendment note: {{ $law->legal_status }}</p>@endif
        @if ($relatedLaws->isNotEmpty())<section class="related-laws"><h2>Related laws in {{ $law->category->category_name }}</h2><ul>@foreach ($relatedLaws as $related)<li><a href="{{ route('laws.show', $related) }}">{{ $related->law_type }} No. {{ $related->law_number }} — {{ $related->official_title }}</a></li>@endforeach</ul></section>@endif
        <aside class="law-disclaimer">This information is for general legal information and educational purposes, not a substitute for professional legal advice. Check the linked source for amendments and current legal effect.</aside>
    </article>
@endsection
