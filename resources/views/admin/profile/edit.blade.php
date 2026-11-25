@extends('admin.layouts.app')

@section('title', 'Profile')
@section('section', 'Profile')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Public profile</p><h1>About the lawyer</h1></div></div>
    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="admin-form">
        @csrf
        @method('PUT')
        <section class="form-section">
            <h2>Identity</h2>
            <div class="row g-4">
                <div class="col-md-6"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="email" class="form-label">Account email</label><input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="professional_title" class="form-label">Professional title</label><input id="professional_title" name="professional_title" value="{{ old('professional_title', $profile?->professional_title) }}" class="form-control @error('professional_title') is-invalid @enderror">@error('professional_title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="photo" class="form-label">Portrait photograph</label><input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="form-control @error('photo') is-invalid @enderror">@error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror @if($profile?->photo_path)<small class="form-hint">A portrait is currently saved.</small>@endif</div>
                <div class="col-12"><label for="short_bio" class="form-label">Short introduction</label><textarea id="short_bio" name="short_bio" rows="3" maxlength="500" class="form-control @error('short_bio') is-invalid @enderror">{{ old('short_bio', $profile?->short_bio) }}</textarea>@error('short_bio')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label for="biography" class="form-label">Biography</label><textarea id="biography" name="biography" rows="8" class="form-control @error('biography') is-invalid @enderror">{{ old('biography', $profile?->biography) }}</textarea>@error('biography')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="education" class="form-label">Educational background</label><textarea id="education" name="education" rows="5" class="form-control @error('education') is-invalid @enderror">{{ old('education', $profile?->education) }}</textarea>@error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="professional_experience" class="form-label">Professional experience</label><textarea id="professional_experience" name="professional_experience" rows="5" class="form-control @error('professional_experience') is-invalid @enderror">{{ old('professional_experience', $profile?->professional_experience) }}</textarea>@error('professional_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
        </section>
        <section class="form-section">
            <h2>Contact details</h2>
            <div class="row g-4">
                <div class="col-md-6"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" value="{{ old('phone', $profile?->phone) }}" class="form-control @error('phone') is-invalid @enderror">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="public_email" class="form-label">Public email</label><input id="public_email" type="email" name="public_email" value="{{ old('public_email', $profile?->public_email) }}" class="form-control @error('public_email') is-invalid @enderror">@error('public_email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label for="office_address" class="form-label">Office address</label><textarea id="office_address" name="office_address" rows="2" class="form-control @error('office_address') is-invalid @enderror">{{ old('office_address', $profile?->office_address) }}</textarea>@error('office_address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
        </section>
        <button class="btn btn-ink">Save profile</button>
    </form>
@endsection
