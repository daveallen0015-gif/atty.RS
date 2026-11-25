@extends('public.layouts.app')

@section('title', $content->get('contact_heading', 'Contact'))

@section('content')
    <section class="interior-heading"><p class="public-eyebrow">Correspondence</p><h1>{{ $content->get('contact_heading') }}</h1>@if (filled($content->get('contact_intro')))<p class="interior-intro">{{ $content->get('contact_intro') }}</p>@endif</section>
    <section class="contact-layout">
        <div class="contact-details">@if ($profile?->phone)<p><span>Telephone</span><a href="tel:{{ $profile->phone }}">{{ $profile->phone }}</a></p>@endif @if ($profile?->public_email)<p><span>Email</span><a href="mailto:{{ $profile->public_email }}">{{ $profile->public_email }}</a></p>@endif @if ($profile?->office_address)<p><span>Office</span><span>{{ $profile->office_address }}</span></p>@endif</div>
        @include('public.contact._form')
    </section>
@endsection
