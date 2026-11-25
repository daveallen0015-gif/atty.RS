@extends('public.layouts.app')

@section('title', $content->get('about_heading', 'About'))

@section('content')
    <section class="interior-heading about-page-heading"><p class="public-eyebrow">{{ $profile?->professional_title }}</p><h1>{{ $content->get('about_heading') }}</h1></section>
    <section class="biography-layout about-profile-layout {{ $profile?->photo_path ? 'has-portrait' : 'no-portrait' }}">
        @if ($profile?->photo_path)<figure class="biography-portrait"><img src="{{ asset('storage/'.$profile->photo_path) }}" alt="Portrait of {{ $lawyer?->name }}"></figure>@endif
        <div class="biography-copy">
            @if (filled($content->get('about_body')))<p class="standfirst">{{ $content->get('about_body') }}</p>@endif
            @if (filled($profile?->biography))<div class="prose">{{ $profile->biography }}</div>@endif
        </div>
    </section>
    @if (filled($profile?->education) || filled($profile?->professional_experience))
        <section class="about-details" aria-label="Education and professional experience">
            <p class="public-eyebrow">Background</p>
            <div class="about-detail-grid">
                @if (filled($profile?->education))
                    <section class="profile-detail-block"><h2>Education</h2><div class="prose">{{ $profile->education }}</div></section>
                @endif
                @if (filled($profile?->professional_experience))
                    <section class="profile-detail-block"><h2>Professional Experience</h2><div class="prose">{{ $profile->professional_experience }}</div></section>
                @endif
            </div>
        </section>
    @endif
@endsection
