@extends('admin.layouts.app')

@section('title', 'Philippine Laws')
@section('section', 'Philippine Laws')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Legal library</p><h1>Philippine Laws</h1></div><a class="btn btn-ink" href="{{ route('admin.laws.create') }}">Add a law</a></div>
    <form method="GET" class="inquiry-filter mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5"><label class="form-label" for="q">Search title or law number</label><input id="q" name="q" value="{{ request('q') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label" for="category">Category</label><select id="category" name="category" class="form-select"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->category_name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label" for="status">Status</label><select id="status" name="status" class="form-select"><option value="">All</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select></div>
            <div class="col-md-2 d-flex gap-2"><button class="btn btn-ink">Filter</button><a class="text-link align-self-center" href="{{ route('admin.laws.index') }}">Clear</a></div>
        </div>
    </form>
    @if ($laws->isEmpty())<p class="empty-state">No law references found. Add a reference or change the filters.</p>
    @else
        <div class="table-responsive"><table class="table editorial-table align-middle"><thead><tr><th>Reference</th><th>Category</th><th>Approved</th><th>Visibility</th><th></th></tr></thead><tbody>
            @foreach ($laws as $law)<tr><td><strong>{{ $law->law_type }} No. {{ $law->law_number }}</strong><div>{{ $law->official_title }}</div></td><td>{{ $law->category->category_name }}</td><td>{{ $law->date_approved?->format('M j, Y') ?? '—' }}</td><td><span class="status-label">{{ $law->is_published ? 'Published' : 'Draft' }}</span></td><td class="text-end"><a class="text-link" href="{{ route('admin.laws.edit', $law) }}">Edit</a></td></tr>@endforeach
        </tbody></table></div>
        {{ $laws->links('pagination::bootstrap-5') }}
    @endif
@endsection
