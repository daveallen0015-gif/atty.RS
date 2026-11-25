@extends('admin.layouts.app')

@section('title', 'Services')
@section('section', 'Services')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Legal Services</p><h1>Legal services</h1></div><a class="btn btn-ink" href="{{ route('admin.services.create') }}">Add a service</a></div>
    <p class="form-hint">Draft sample services are hidden from the public site. Review and publish only services the lawyer offers.</p>
    @if ($services->isEmpty())
        <p class="empty-state">Add the first service to describe your legal services.</p>
    @else
        <div class="table-responsive"><table class="table editorial-table align-middle"><thead><tr><th>Order</th><th>Service</th><th>Visibility</th><th></th></tr></thead><tbody>
            @foreach ($services as $service)
                <tr><td>{{ $service->sort_order }}</td><td>@if($service->image_path)<img class="admin-service-thumbnail" src="{{ asset('storage/'.$service->image_path) }}" alt="">@endif<strong>{{ $service->title }}</strong><div class="text-muted small">{{ $service->summary }}</div><div class="text-muted small">{{ $icons[$service->icon] ?? $icons['scale'] }}</div></td><td><span class="admin-status {{ $service->is_published ? 'admin-status-published' : 'admin-status-draft' }}">{{ $service->is_published ? 'Published' : 'Draft' }}</span></td><td class="text-end"><a class="text-link" href="{{ route('admin.services.edit', $service) }}">Edit</a></td></tr>
            @endforeach
        </tbody></table></div>
    @endif
@endsection
