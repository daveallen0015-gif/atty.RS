@extends('admin.layouts.app')

@section('title', 'Inquiries')
@section('section', 'Inquiries')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Correspondence</p><h1>Visitor inquiries</h1></div></div>
    <form method="GET" action="{{ route('admin.inquiries.index') }}" class="inquiry-filter"><label for="status" class="form-label">Show status</label><select id="status" name="status" class="form-select" onchange="this.form.submit()"><option value="">All inquiries</option>@foreach (\App\Models\Inquiry::STATUSES as $status)<option value="{{ $status }}" @selected($selectedStatus === $status)>{{ \App\Models\Inquiry::STATUS_LABELS[$status] }}</option>@endforeach</select></form>
    @if ($inquiries->isEmpty())
        <p class="empty-state">There are no inquiries for this view.</p>
    @else
        <div class="table-responsive"><table class="table editorial-table align-middle"><thead><tr><th>Received</th><th>From</th><th>Subject</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach ($inquiries as $inquiry)
                <tr><td>{{ $inquiry->created_at->format('M j, Y') }}</td><td>{{ $inquiry->name }}<div class="text-muted small">{{ $inquiry->email }}</div></td><td>{{ $inquiry->subject }}</td><td><span class="admin-status admin-status-{{ $inquiry->status }}">{{ \App\Models\Inquiry::STATUS_LABELS[$inquiry->status] ?? ucfirst($inquiry->status) }}</span></td><td class="text-end"><a class="text-link" href="{{ route('admin.inquiries.show', $inquiry) }}">Review</a></td></tr>
            @endforeach
        </tbody></table></div>
        {{ $inquiries->links() }}
    @endif
@endsection
