@extends('public.layouts.app')

@section('title', 'Private Chat')

@section('content')
    <section class="chat-page">
        <header class="chat-page-heading">
            <p class="public-eyebrow">Private conversation</p>
            <h1>Chat with the Lawyer</h1>
            <p>Send a message to begin a private conversation. Your chat is available only in this browser session.</p>
        </header>

        @if (!$conversation || $conversation->status === 'closed')
            @if ($conversation?->status === 'closed')
                <p class="chat-closed-notice">Your previous conversation is closed. You can start a new one below.</p>
            @endif
            <form method="POST" action="{{ route('chat.start') }}" class="public-contact-form chat-start-form" data-confirm-title="Start private chat?" data-confirm-message="Your message will start a private conversation with the lawyer." data-confirm-button="Yes, start chat">
                @csrf
                <h2>Start a conversation</h2>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="visitor_name">Your name</label><input id="visitor_name" name="visitor_name" value="{{ old('visitor_name') }}" class="form-control @error('visitor_name') is-invalid @enderror" required maxlength="255">@error('visitor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6"><label class="form-label" for="visitor_email">Email (optional)</label><input id="visitor_email" type="email" name="visitor_email" value="{{ old('visitor_email') }}" class="form-control @error('visitor_email') is-invalid @enderror" maxlength="255">@error('visitor_email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-12"><label class="form-label" for="body">Message</label><textarea id="body" name="body" rows="5" class="form-control @error('body') is-invalid @enderror" required maxlength="5000">{{ old('body') }}</textarea>@error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-12"><button class="btn btn-ink">Start private chat</button></div>
                </div>
            </form>
        @else
            <section class="visitor-chat-panel">
                <div class="visitor-chat-topline"><div><strong>{{ $conversation->visitor_name }}</strong><span>Private chat</span></div><span class="chat-state chat-state-{{ $conversation->status }}">{{ ucfirst($conversation->status) }}</span></div>
                <div class="chat-message-list" id="visitor-chat-messages" data-last-id="{{ $conversation->messages->last()?->id ?? 0 }}">
                    @foreach ($conversation->messages as $message)
                        <article class="chat-message chat-message-{{ $message->sender_type }}" data-message-id="{{ $message->id }}"><div>{{ $message->body }}</div><time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('M j, g:i a') }}</time></article>
                    @endforeach
                </div>
                @if ($conversation->status === 'open')
                    <form id="visitor-chat-form" class="visitor-chat-form" method="POST" action="{{ route('chat.messages.store', $conversation) }}" data-poll-url="{{ route('chat.messages', $conversation) }}" data-confirm-title="Send message?" data-confirm-message="Your message will be sent in this private conversation." data-confirm-button="Yes, send message">
                        @csrf
                        <label class="visually-hidden" for="visitor-chat-body">Your message</label>
                        <textarea id="visitor-chat-body" name="body" rows="2" maxlength="5000" placeholder="Write a message…" required></textarea>
                        <button class="btn btn-ink">Send</button>
                    </form>
                @else
                    <p class="chat-closed-notice">This conversation has been closed by the lawyer.</p>
                @endif
            </section>
            <script>
                (() => {
                    const form = document.querySelector('#visitor-chat-form');
                    if (!form) return;
                    const list = document.querySelector('#visitor-chat-messages');
                    const body = form.elements.body;
                    let lastId = Number(list.dataset.lastId);

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

                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();
                        const data = new FormData(form);
                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': data.get('_token'), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                                body: data,
                            });
                            if (response.ok) {
                                appendMessage(await response.json());
                                body.value = '';
                            }
                        } catch (error) {}
                    });

                    window.setInterval(async () => {
                        try {
                            const response = await fetch(form.dataset.pollUrl + '?after=' + lastId, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                            if (response.ok) (await response.json()).forEach(appendMessage);
                        } catch (error) {}
                    }, 5000);
                })();
            </script>
        @endif
        <p class="chat-privacy-note">Please avoid sending highly sensitive personal or financial information in this chat.</p>
    </section>
@endsection
