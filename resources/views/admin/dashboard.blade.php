@extends('admin.layouts.app')

@section('title', 'Overview')
@section('section', 'Overview')

@section('content')
    <div class="page-heading dashboard-heading">
        <div><p class="eyebrow">Your website at a glance</p><h1>Good day, {{ auth()->user()->name }}.</h1></div>
        <a class="btn btn-ink" href="{{ route('admin.inquiries.index') }}">Review inquiries</a>
    </div>

    <section class="stat-grid dashboard-stats" aria-label="Website activity">
        <a class="stat-panel" href="{{ route('admin.inquiries.index') }}"><span class="stat-label">Total inquiries</span><strong>{{ $inquiryCount }}</strong><span class="stat-note">All visitor inquiries</span></a>
        <a class="stat-panel" href="{{ route('admin.inquiries.index', ['status' => 'new']) }}"><span class="stat-label">Unread inquiries</span><strong>{{ $unreadInquiryCount }}</strong><span class="stat-note">Awaiting review</span></a>
        <a class="stat-panel" href="{{ route('admin.chats.index', ['status' => 'open']) }}"><span class="stat-label">Active conversations</span><strong>{{ $activeConversationCount }}</strong><span class="stat-note">Open visitor chats</span></a>
        <a class="stat-panel" href="{{ route('admin.services.index') }}"><span class="stat-label">Legal services</span><strong>{{ $serviceCount }}</strong><span class="stat-note">Published and draft services</span></a>
    </section>

    <div class="dashboard-columns">
        <section class="dashboard-panel">
            <div class="section-heading"><h2>Recent inquiries</h2><a href="{{ route('admin.inquiries.index') }}">View all</a></div>
            @if ($recentInquiries->isEmpty())
                <p class="empty-state">New visitor inquiries will appear here.</p>
            @else
                <div class="table-responsive"><table class="table editorial-table align-middle"><thead><tr><th>Visitor</th><th>Subject</th><th>Status</th></tr></thead><tbody>
                    @foreach ($recentInquiries as $inquiry)
                        <tr><td><a class="dashboard-row-link" href="{{ route('admin.inquiries.show', $inquiry) }}">{{ $inquiry->name }}</a><small>{{ $inquiry->created_at->format('M j, Y') }}</small></td><td>{{ \Illuminate\Support\Str::limit($inquiry->subject, 38) }}</td><td><span class="admin-status admin-status-{{ $inquiry->status }}">{{ \App\Models\Inquiry::STATUS_LABELS[$inquiry->status] ?? ucfirst($inquiry->status) }}</span></td></tr>
                    @endforeach
                </tbody></table></div>
            @endif
        </section>

        <section class="dashboard-panel">
            <div class="section-heading"><h2>Recent chat conversations</h2><a href="{{ route('admin.chats.index') }}">View all</a></div>
            @if ($recentConversations->isEmpty())
                <p class="empty-state">Visitor chats will appear here when someone starts a conversation.</p>
            @else
                <div class="dashboard-activity-list">@foreach ($recentConversations as $conversation)
                    <a class="dashboard-activity-row" href="{{ route('admin.chats.show', $conversation) }}">
                        <span class="dashboard-activity-copy"><strong>{{ $conversation->visitor_name }}</strong><small>{{ \Illuminate\Support\Str::limit($conversation->latestMessage?->body ?? 'No messages yet', 76) }}</small></span>
                        <span class="dashboard-activity-meta">@if($conversation->unread_visitor_messages_count)<b>{{ $conversation->unread_visitor_messages_count }} new</b>@endif<small>{{ $conversation->last_message_at?->diffForHumans() }}</small></span>
                    </a>
                @endforeach</div>
            @endif
        </section>

        <section class="dashboard-panel">
            <div class="section-heading"><h2>Latest website updates</h2><a href="{{ route('admin.settings.edit') }}">Edit content</a></div>
            @if ($recentUpdates->isEmpty())
                <p class="empty-state">Saved website content updates will appear here.</p>
            @else
                <div class="dashboard-update-list">@foreach ($recentUpdates as $update)
                    <div><span>{{ $update->title }}</span><time>{{ $update->updated_at->diffForHumans() }}</time></div>
                @endforeach</div>
            @endif
        </section>

        <section class="dashboard-panel quick-actions-panel">
            <div class="section-heading"><h2>Quick actions</h2></div>
            <div class="quick-actions">
                <a href="{{ route('admin.profile.edit') }}">Update lawyer profile <span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.services.create') }}">Add a legal service <span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.laws.create') }}">Add a law reference <span aria-hidden="true">→</span></a>
                <a href="{{ route('admin.settings.edit') }}">Open System Settings <span aria-hidden="true">→</span></a>
            </div>
        </section>
    </div>
@endsection
