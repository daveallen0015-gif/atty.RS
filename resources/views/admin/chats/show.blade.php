@extends('admin.layouts.app')

@section('title', 'Chat with '.$conversation->visitor_name)
@section('section', 'Chat Management')

@section('content')
    <div class="page-heading chat-admin-heading">
        <div><a class="chat-back-link" href="{{ route('admin.chats.index') }}">← All conversations</a><h1>{{ $conversation->visitor_name }}</h1><p class="eyebrow">{{ $conversation->visitor_email ?: 'Visitor did not provide an email' }}</p></div>
        <div class="chat-admin-actions">
            <span id="conversation-status" class="chat-state chat-state-{{ $conversation->status }}">{{ ucfirst($conversation->status) }}</span>
            <form id="conversation-status-form" method="POST" action="{{ route('admin.chats.status', $conversation) }}" data-confirm-title="{{ $conversation->status === 'open' ? 'Close conversation?' : 'Reopen conversation?' }}" data-confirm-message="The conversation status will be changed." data-confirm-button="Yes, continue">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="{{ $conversation->status === 'open' ? 'closed' : 'open' }}">
                <button class="btn btn-outline-navy">{{ $conversation->status === 'open' ? 'Close conversation' : 'Reopen conversation' }}</button>
            </form>
        </div>
    </div>
    <section class="admin-chat-panel">
        <div id="admin-chat-messages" class="chat-message-list" data-last-id="{{ $conversation->messages->last()?->id ?? 0 }}" data-poll-url="{{ route('admin.chats.messages', $conversation) }}">
            @foreach ($conversation->messages as $message)
                <article class="chat-message chat-message-{{ $message->sender_type }}" data-message-id="{{ $message->id }}"><div>{{ $message->body }}</div><time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('M j, Y · g:i a') }}</time></article>
            @endforeach
        </div>
        <p id="chat-action-feedback" class="chat-action-feedback" aria-live="polite"></p>
        @if ($conversation->status === 'open')
            <form id="admin-chat-reply" class="admin-chat-reply" method="POST" action="{{ route('admin.chats.reply', $conversation) }}" data-confirm-title="Send reply?" data-confirm-message="Your reply will be sent to this visitor." data-confirm-button="Yes, send reply">
                @csrf
                <label class="visually-hidden" for="admin-chat-body">Reply to {{ $conversation->visitor_name }}</label>
                <textarea id="admin-chat-body" name="body" rows="3" maxlength="5000" placeholder="Write your reply…" required></textarea>
                <button class="btn btn-ink">Send reply</button>
            </form>
        @else
            <p class="chat-closed-notice">This conversation is closed. Reopen it to send a reply.</p>
        @endif
    </section>
    <script>
        (() => {
            const list = document.querySelector('#admin-chat-messages');
            const feedback = document.querySelector('#chat-action-feedback');
            let lastId = Number(list.dataset.lastId);
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            const appendMessage = (message) => {
                if (message.id <= lastId) return;
                const article = document.createElement('article');
                article.className = 'chat-message chat-message-' + message.sender_type;
                article.dataset.messageId = message.id;
                const text = document.createElement('div');
                text.textContent = message.body;
                const time = document.createElement('time');
                time.dateTime = message.created_at;
                time.textContent = new Date(message.created_at).toLocaleString();
                article.append(text, time);
                list.append(article);
                lastId = message.id;
                list.scrollTop = list.scrollHeight;
            };

            const replyForm = document.querySelector('#admin-chat-reply');
            replyForm?.addEventListener('submit', async (event) => {
                event.preventDefault();
                feedback.textContent = '';
                const data = new FormData(replyForm);
                try {
                    const response = await fetch(replyForm.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: data,
                    });
                    if (!response.ok) throw new Error('Your reply could not be sent.');
                    appendMessage(await response.json());
                    replyForm.elements.body.value = '';
                } catch (error) {
                    feedback.textContent = error.message;
                }
            });

            const statusForm = document.querySelector('#conversation-status-form');
            statusForm?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const data = new FormData(statusForm);
                data.set('_method', 'PATCH');
                try {
                    const response = await fetch(statusForm.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: data,
                    });
                    if (!response.ok) throw new Error('Conversation status could not be updated.');
                    window.location.reload();
                } catch (error) {
                    feedback.textContent = error.message;
                }
            });

            window.setInterval(async () => {
                try {
                    const response = await fetch(list.dataset.pollUrl + '?after=' + lastId, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                    if (response.ok) (await response.json()).forEach(appendMessage);
                } catch (error) {}
            }, 5000);
        })();
    </script>
@endsection
