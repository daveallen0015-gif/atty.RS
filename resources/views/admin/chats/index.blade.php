@extends('admin.layouts.app')

@section('title', 'Chat Management')
@section('section', 'Chat Management')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Private visitor messages</p><h1>Chat Management</h1></div></div>
    <form method="GET" class="admin-filter-bar">
        <label for="chat-status">Conversation status</label>
        <select id="chat-status" name="status" class="form-select" onchange="this.form.submit()">
            <option value="">All conversations</option>
            <option value="open" @selected($selectedStatus === 'open')>Open</option>
            <option value="closed" @selected($selectedStatus === 'closed')>Closed</option>
        </select>
    </form>
    @if ($conversations->isEmpty())
        <p class="empty-state">No chat conversations yet. New visitor chats will appear here.</p>
    @else
        <div class="conversation-list">
            @foreach ($conversations as $conversation)
                <a class="conversation-row" href="{{ route('admin.chats.show', $conversation) }}">
                    <span class="conversation-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($conversation->visitor_name, 0, 1)) }}</span>
                    <span class="conversation-summary"><strong>{{ $conversation->visitor_name }}</strong><small>{{ $conversation->visitor_email ?: 'No email provided' }}</small><span>{{ \Illuminate\Support\Str::limit($conversation->latestMessage?->body ?? 'No messages', 100) }}</span></span>
                    <span class="conversation-meta"><span class="chat-state chat-state-{{ $conversation->status }}">{{ ucfirst($conversation->status) }}</span>@if($conversation->unread_visitor_messages_count)<b class="unread-indicator">{{ $conversation->unread_visitor_messages_count }} unread</b>@endif<small>{{ $conversation->last_message_at?->diffForHumans() }}</small></span>
                </a>
            @endforeach
        </div>
        {{ $conversations->links('pagination::bootstrap-5') }}
    @endif
@endsection
