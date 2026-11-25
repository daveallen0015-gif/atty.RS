@extends('public.layouts.app')

@section('title', 'Philippine Laws and Legal References')

@section('content')
    <section class="law-library-heading">
        <p class="public-eyebrow">LEGAL KNOWLEDGE AND REFERENCES</p>
        <h1>Philippine Laws and<br><em>Legal References</em></h1>
        <p>Explore the laws of the Philippines, including Republic Acts, Presidential Decrees, Executive Orders, legal codes, and other statutory provisions. Access organized legal references to better understand the laws that govern rights, responsibilities, and various aspects of society.</p>
    </section>

    <section class="law-category-section" aria-labelledby="law-category-title">
        <div class="law-section-heading"><p class="public-eyebrow">Browse by subject</p><h2 id="law-category-title">Areas of Philippine law</h2></div>
        <div class="law-category-grid">
            @foreach ($categories as $category)
                <a class="law-category-card {{ $selectedCategory === $category->slug ? 'selected' : '' }}" href="{{ route('laws.index', array_filter(['category' => $category->slug, 'q' => $search])) }}">
                    <span class="law-category-icon" aria-hidden="true">@include('public.laws._icon', ['category' => $category])</span>
                    <span class="law-category-name">{{ $category->category_name }}</span>
                    <span class="law-category-description">{{ $category->description }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="law-results-section" aria-labelledby="law-results-title">
        <div class="law-section-heading"><p class="public-eyebrow">Reference collection</p><h2 id="law-results-title">Browse the laws</h2></div>
        <form class="law-search-form" method="GET" action="{{ route('laws.index') }}">
            <label class="visually-hidden" for="law-search">Search titles, law numbers, or keywords</label>
            <input id="law-search" type="search" name="q" value="{{ $search }}" placeholder="Search titles, RA / PD / EO numbers, or keywords">
            <label class="visually-hidden" for="law-category">Filter by category</label>
            <select id="law-category" name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->category_name }}</option>@endforeach</select>
            <button type="submit">Search</button>
            @if ($search !== '' || $selectedCategory !== '')<a id="law-clear-filters" href="{{ route('laws.index') }}">Clear filters</a>@endif
        </form>
        <div id="law-async-results" aria-live="polite" aria-busy="false">
            @include('public.laws._results', ['laws' => $laws])
        </div>
    </section>
    <aside class="law-disclaimer">This library is for general legal information and educational purposes. It is not a substitute for legal advice. Laws may be amended, repealed, or interpreted by later issuances and court decisions. Check the linked source and consult a lawyer about your circumstances.</aside>

    <script>
        (() => {
            const form = document.querySelector('.law-search-form');
            const results = document.querySelector('#law-async-results');
            const categoryCards = document.querySelectorAll('.law-category-card');
            let activeRequest;
            let searchTimer;

            const currentUrl = () => {
                const url = new URL(form.action);
                const values = new FormData(form);
                for (const [key, value] of values) {
                    if (value.trim()) url.searchParams.set(key, value.trim());
                }
                return url;
            };

            const syncControls = (url) => {
                form.elements.q.value = url.searchParams.get('q') || '';
                form.elements.category.value = url.searchParams.get('category') || '';
                document.querySelector('#law-clear-filters')?.remove();
                if (url.searchParams.has('q') || url.searchParams.has('category')) {
                    const clear = document.createElement('a');
                    clear.id = 'law-clear-filters';
                    clear.href = form.action;
                    clear.textContent = 'Clear filters';
                    form.append(clear);
                }
                categoryCards.forEach((card) => {
                    card.classList.toggle('selected', new URL(card.href).searchParams.get('category') === url.searchParams.get('category'));
                });
            };

            const loadResults = async (url, historyMode = 'push') => {
                activeRequest?.abort();
                const request = new AbortController();
                activeRequest = request;
                results.setAttribute('aria-busy', 'true');
                results.classList.add('is-loading');

                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                        signal: request.signal,
                    });
                    if (!response.ok) throw new Error('Could not load law references.');
                    results.innerHTML = await response.text();
                    syncControls(url);
                    if (historyMode === 'push') history.pushState({}, '', url);
                    if (historyMode === 'replace') history.replaceState({}, '', url);
                } catch (error) {
                    if (error.name !== 'AbortError') results.innerHTML = '<p class="law-empty-state">The results could not be loaded. Please try again.</p>';
                } finally {
                    if (activeRequest === request) {
                        results.setAttribute('aria-busy', 'false');
                        results.classList.remove('is-loading');
                    }
                }
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                loadResults(currentUrl());
            });
            form.elements.category.addEventListener('change', () => loadResults(currentUrl()));
            form.elements.q.addEventListener('input', () => {
                window.clearTimeout(searchTimer);
                searchTimer = window.setTimeout(() => loadResults(currentUrl(), 'replace'), 300);
            });
            document.addEventListener('click', (event) => {
                const categoryCard = event.target.closest('.law-category-card');
                const pageLink = event.target.closest('#law-async-results .pagination a');
                const clearLink = event.target.closest('#law-clear-filters');

                if (categoryCard) {
                    event.preventDefault();
                    const url = currentUrl();
                    url.searchParams.set('category', new URL(categoryCard.href).searchParams.get('category'));
                    loadResults(url);
                } else if (pageLink || clearLink) {
                    event.preventDefault();
                    loadResults(new URL(clearLink ? form.action : pageLink.href));
                }
            });
            window.addEventListener('popstate', () => loadResults(new URL(window.location.href), false));
        })();
    </script>
@endsection
