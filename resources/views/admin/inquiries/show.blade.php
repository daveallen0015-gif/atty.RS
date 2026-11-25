@extends('admin.layouts.app')

@section('title', 'Inquiry')
@section('section', 'Inquiries')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Received {{ $inquiry->created_at->format('F j, Y · g:i a') }}</p><h1>{{ $inquiry->subject }}</h1></div></div>
    <div class="inquiry-detail">
        <dl class="inquiry-contact"><div><dt>From</dt><dd>{{ $inquiry->name }}</dd></div><div><dt>Email</dt><dd><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd></div>@if($inquiry->phone)<div><dt>Phone</dt><dd><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a></dd></div>@endif<div><dt>Status</dt><dd><span class="admin-status admin-status-{{ $inquiry->status }}">{{ \App\Models\Inquiry::STATUS_LABELS[$inquiry->status] ?? ucfirst($inquiry->status) }}</span></dd></div></dl>
        <section class="inquiry-message"><h2>Message</h2><p>{{ $inquiry->message }}</p></section>
        <div class="inquiry-actions">
            <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}" class="inquiry-status-form" data-confirm-title="Update inquiry status?" data-confirm-message="The inquiry status will be changed." data-confirm-button="Yes, update status">@csrf @method('PATCH')<div><label for="status" class="form-label">Update status</label><select id="status" name="status" class="form-select">@foreach (\App\Models\Inquiry::STATUSES as $status)<option value="{{ $status }}" @selected(old('status', $inquiry->status) === $status)>{{ \App\Models\Inquiry::STATUS_LABELS[$status] }}</option>@endforeach</select>@error('status')<div class="text-danger small">{{ $message }}</div>@enderror</div><button class="btn btn-ink">Save status</button><a class="text-link" href="{{ route('admin.inquiries.index') }}">Back to inquiries</a></form>
            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" data-confirm-title="Delete inquiry?" data-confirm-message="This inquiry will be permanently deleted. This cannot be undone." data-confirm-button="Yes, delete inquiry" data-confirm-icon="warning">@csrf @method('DELETE')<button class="btn btn-outline-danger">Delete inquiry</button></form>
        </div>
    </div>
@endsection
