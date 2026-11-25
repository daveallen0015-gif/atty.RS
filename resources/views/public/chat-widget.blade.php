<div class="chat-widget" data-widget-url="{{ route('chat.widget') }}" data-start-url="{{ route('chat.start') }}">
    <section id="chat-widget-panel" class="chat-widget-panel" role="dialog" aria-label="Chat with the Lawyer" aria-modal="false" hidden>
        <header class="chat-widget-header">
            <span class="chat-widget-emblem"><img src="{{ asset('images/lawyer-emblem.svg') }}" alt=""></span>
            <span class="chat-widget-title"><strong>Chat with Us</strong><small>We're here to help!</small></span>
            <button class="chat-widget-control" type="button" data-widget-hide aria-label="Minimize chat">−</button>
            <button class="chat-widget-control chat-widget-close" type="button" data-widget-hide aria-label="Close chat">×</button>
        </header>
        <div id="chat-widget-content" class="chat-widget-content" aria-live="polite">
            <p class="chat-widget-loading">Opening your private chat…</p>
        </div>
        <footer class="chat-widget-footer"><span aria-hidden="true">◆</span> Your conversation is private and secure.</footer>
    </section>
    @if (request()->routeIs('home'))
        <div class="chat-widget-welcome" id="chat-widget-welcome" role="status" hidden>
            <button class="chat-widget-welcome-open" type="button">Hello! How can we help you today?</button>
            <button class="chat-widget-welcome-close" type="button" aria-label="Dismiss greeting">×</button>
        </div>
    @endif
    <button class="chat-widget-launcher" type="button" aria-controls="chat-widget-panel" aria-expanded="false">
        <span class="chat-widget-launcher-icon" aria-hidden="true">•••</span><span>Chat with Us</span>
    </button>
</div>
<script>
    (() => {
        const widget = document.querySelector('.chat-widget');
        if (!widget) return;
        const panel = widget.querySelector('#chat-widget-panel');
        const content = widget.querySelector('#chat-widget-content');
        const launcher = widget.querySelector('.chat-widget-launcher');
        let thread = null;
        let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let conversation = null;
        let lastId = 0;
        let loaded = false;
        let busy = false;

        const element = (tag, className, text) => {
            const node = document.createElement(tag);
            if (className) node.className = className;
            if (text) node.textContent = text;
            return node;
        };

        const appendMessage = (message) => {
            if (message.id <= lastId) return;
            const row = element('article', 'chat-widget-message chat-widget-message-' + message.sender_type);
            row.dataset.messageId = message.id;
            const text = element('div', 'chat-widget-message-body', message.body);
            const time = element('time', '', new Date(message.created_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }));
            time.dateTime = message.created_at;
            row.append(text, time);
            (thread || content).append(row);
            lastId = message.id;
            const scrollArea = thread || content;
            scrollArea.scrollTop = scrollArea.scrollHeight;
        };

        const addField = (form, labelText, name, type, required) => {
            const label = element('label', 'chat-widget-label', labelText);
            const field = type === 'textarea' ? element('textarea', 'chat-widget-input') : element('input', 'chat-widget-input');
            field.name = name;
            field.required = required;
            if (type !== 'textarea') field.type = type;
            if (name === 'visitor_name') field.autocomplete = 'name';
            if (name === 'visitor_email') field.autocomplete = 'email';
            label.append(field);
            form.append(label);
        };

        const showStartForm = (closed = false) => {
            thread = element('div', 'chat-widget-thread');
            content.append(thread);
            if (closed) content.append(element('p', 'chat-widget-closed', 'Your previous chat is closed. Start a new conversation below.'));
            const greeting = element('div', 'chat-widget-message chat-widget-message-lawyer');
            greeting.append(element('div', 'chat-widget-message-body', 'Hello! How can we help you today?'));
            thread.append(greeting);
            const form = element('form', 'chat-widget-form');
            addField(form, 'Your name', 'visitor_name', 'text', true);
            addField(form, 'Email (optional)', 'visitor_email', 'email', false);
            addField(form, 'Your message', 'body', 'textarea', true);
            const button = element('button', 'chat-widget-send', 'Start chat');
            button.type = 'submit';
            form.append(button);
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (busy) return;
                const confirmation = await Swal.fire({ title: 'Start private chat?', text: 'Your message will start a private conversation with the lawyer.', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, start chat', cancelButtonText: 'No, cancel', reverseButtons: true });
                if (!confirmation.isConfirmed) return;
                busy = true;
                button.disabled = true;
                const formData = new FormData(form);
                try {
                    const response = await fetch(widget.dataset.startUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: formData,
                    });
                    if (!response.ok) throw new Error('We could not start your chat. Please try again.');
                    const data = await response.json();
                    csrfToken = data.csrf_token || csrfToken;
                    renderConversation(data.conversation);
                } catch (error) {
                    content.append(element('p', 'chat-widget-error', error.message));
                    button.disabled = false;
                } finally {
                    busy = false;
                }
            });
            content.append(form);
        };

        const renderConversation = (data) => {
            conversation = data;
            lastId = 0;
            thread = null;
            content.replaceChildren();
            if (!conversation || conversation.status === 'closed') {
                showStartForm(Boolean(conversation));
                return;
            }
            thread = element('div', 'chat-widget-thread');
            content.append(thread);
            conversation.messages.forEach(appendMessage);
            const form = element('form', 'chat-widget-form chat-widget-reply-form');
            const input = element('input', 'chat-widget-input');
            input.name = 'body';
            input.type = 'text';
            input.maxLength = 5000;
            input.placeholder = 'Type your message…';
            input.required = true;
            const button = element('button', 'chat-widget-send chat-widget-send-icon', '➤');
            button.type = 'submit';
            button.setAttribute('aria-label', 'Send message');
            form.append(input, button);
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (busy) return;
                const confirmation = await Swal.fire({ title: 'Send message?', text: 'Your message will be sent in this private conversation.', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, send message', cancelButtonText: 'No, cancel', reverseButtons: true });
                if (!confirmation.isConfirmed) return;
                busy = true;
                button.disabled = true;
                try {
                    const response = await fetch(conversation.send_url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: new FormData(form),
                    });
                    if (!response.ok) throw new Error('Your message could not be sent. Please try again.');
                    appendMessage(await response.json());
                    input.value = '';
                } catch (error) {
                    content.append(element('p', 'chat-widget-error', error.message));
                } finally {
                    busy = false;
                    button.disabled = false;
                }
            });
            content.append(form);
            thread.scrollTop = thread.scrollHeight;
        };

        const loadWidget = async () => {
            thread = null;
            content.replaceChildren(element('p', 'chat-widget-loading', 'Opening your private chat…'));
            try {
                const response = await fetch(widget.dataset.widgetUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error('Chat is temporarily unavailable.');
                const data = await response.json();
                csrfToken = data.csrf_token || csrfToken;
                renderConversation(data.conversation);
                loaded = true;
                if (data.conversation && data.conversation.status === 'open') {
                    panel.hidden = false;
                    launcher.setAttribute('aria-expanded', 'true');
                    window.sessionStorage.setItem('lawyer-chat-panel-open', '1');
                }
            } catch (error) {
                content.replaceChildren(element('p', 'chat-widget-error', error.message));
            }
        };

        launcher.addEventListener('click', () => {
            const opening = panel.hidden;
            panel.hidden = !opening;
            launcher.setAttribute('aria-expanded', opening ? 'true' : 'false');
            window.sessionStorage.setItem('lawyer-chat-panel-open', opening ? '1' : '0');
            if (opening && !loaded) loadWidget();
        });
        widget.querySelectorAll('[data-widget-hide]').forEach((button) => button.addEventListener('click', () => {
            panel.hidden = true;
            launcher.setAttribute('aria-expanded', 'false');
            window.sessionStorage.setItem('lawyer-chat-panel-open', '0');
            launcher.focus();
        }));

        if (window.sessionStorage.getItem('lawyer-chat-panel-open') === '1') {
            panel.hidden = false;
            launcher.setAttribute('aria-expanded', 'true');
            loadWidget();
        }

        const welcome = widget.querySelector('#chat-widget-welcome');
        if (welcome) {
            const greetingSeen = 'lawyer-chat-greeting-seen';
            const dismissGreeting = () => {
                welcome.hidden = true;
                window.sessionStorage.setItem(greetingSeen, '1');
            };
            if (!window.sessionStorage.getItem(greetingSeen)) {
                window.setTimeout(() => {
                    if (panel.hidden) welcome.hidden = false;
                }, 1200);
            }
            welcome.querySelector('.chat-widget-welcome-open').addEventListener('click', () => {
                dismissGreeting();
                launcher.click();
            });
            welcome.querySelector('.chat-widget-welcome-close').addEventListener('click', dismissGreeting);
        }

        window.setInterval(async () => {
            if (panel.hidden || !conversation || conversation.status !== 'open') return;
            try {
                const response = await fetch(conversation.poll_url + '?after=' + lastId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                if (response.ok) (await response.json()).forEach(appendMessage);
            } catch (error) {}
        }, 5000);
    })();
</script>
