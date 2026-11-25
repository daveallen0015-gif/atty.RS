<form method="POST" action="{{ $action }}" class="admin-form" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    <section class="form-section"><div class="row g-4">
        <div class="col-12"><label for="title" class="form-label">Service title</label><input id="title" name="title" value="{{ old('title', $service?->title) }}" class="form-control @error('title') is-invalid @enderror" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label for="icon" class="form-label">Service icon</label><select id="icon" name="icon" class="form-select @error('icon') is-invalid @enderror" required>@foreach ($icons as $value => $label)<option value="{{ $value }}" @selected(old('icon', $service?->icon ?? 'scale') === $value)>{{ $label }}</option>@endforeach</select>@error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label for="image" class="form-label">Service image (optional)</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control @error('image') is-invalid @enderror">@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror @if ($service?->image_path)<img class="admin-service-preview mt-2" src="{{ str_starts_with($service->image_path, 'http') ? $service->image_path : asset('storage/'.$service->image_path) }}" alt="Current service image">@endif</div>
        <div class="col-12"><label for="summary" class="form-label">Short summary</label><textarea id="summary" name="summary" rows="2" maxlength="500" class="form-control @error('summary') is-invalid @enderror">{{ old('summary', $service?->summary) }}</textarea>@error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label for="description" class="form-label">Description</label><textarea id="description" name="description" rows="8" class="form-control @error('description') is-invalid @enderror">{{ old('description', $service?->description) }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-4"><label for="sort_order" class="form-label">Display order</label><input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $service?->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror" required>@error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-8 d-flex align-items-end pb-2"><div class="form-check"><input id="is_published" type="checkbox" name="is_published" value="1" class="form-check-input" @checked(old('is_published', $service?->is_published ?? false))><label for="is_published" class="form-check-label">Show this service on the website</label></div></div>
    </div></section>
    <div class="d-flex flex-wrap gap-3"><button class="btn btn-ink">Save service</button><a class="text-link align-self-center" href="{{ route('admin.services.index') }}">Cancel</a>
        @if ($service)<button form="delete-service" class="btn btn-outline-danger ms-auto">Delete</button>@endif
    </div>
</form>
@if ($service)
    <form id="delete-service" method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Delete this service?')">@csrf @method('DELETE')</form>
@endif
