<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\LawyerProfile;
use App\Models\PageContent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $conversation = $this->visitorConversation($request);

        return view('public.chat', [
            'lawyer' => User::query()->where('is_lawyer', true)->first(),
            'profile' => LawyerProfile::query()->with('user')->first(),
            'content' => PageContent::query()->pluck('content', 'key'),
            'conversation' => $conversation?->load('messages'),
        ]);
    }

    public function widget(Request $request): JsonResponse
    {
        $conversation = $this->visitorConversation($request);

        return response()->json([
            'csrf_token' => csrf_token(),
            'conversation' => $conversation
                ? [
                    'id' => $conversation->id,
                    'name' => $conversation->visitor_name,
                    'status' => $conversation->status,
                    'messages' => $conversation->messages()->oldest()->get()->map(fn (ChatMessage $message) => $this->messageData($message)),
                    'send_url' => route('chat.messages.store', $conversation),
                    'poll_url' => route('chat.messages', $conversation),
                ]
                : null,
        ]);
    }

    public function start(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $token = Str::random(64);
        $conversation = ChatConversation::query()->create([
            'visitor_name' => $validated['visitor_name'],
            'visitor_email' => $validated['visitor_email'] ?? null,
            'visitor_token_hash' => hash('sha256', $token),
            'status' => 'open',
            'last_message_at' => now(),
        ]);
        $conversation->messages()->create([
            'sender_type' => 'visitor',
            'body' => $validated['body'],
        ]);

        $request->session()->put('visitor_chat_id', $conversation->id);
        $request->session()->put('visitor_chat_token', $token);

        if ($request->expectsJson()) {
            return response()->json([
                'csrf_token' => csrf_token(),
                'conversation' => [
                    'id' => $conversation->id,
                    'name' => $conversation->visitor_name,
                    'status' => $conversation->status,
                    'messages' => $conversation->messages()->oldest()->get()->map(fn (ChatMessage $message) => $this->messageData($message)),
                    'send_url' => route('chat.messages.store', $conversation),
                    'poll_url' => route('chat.messages', $conversation),
                ],
            ], 201);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Request $request, ChatConversation $conversation): View
    {
        $this->authorizeVisitor($request, $conversation);

        return view('public.chat', [
            'lawyer' => User::query()->where('is_lawyer', true)->first(),
            'profile' => LawyerProfile::query()->with('user')->first(),
            'content' => PageContent::query()->pluck('content', 'key'),
            'conversation' => $conversation->load('messages'),
        ]);
    }

    public function send(Request $request, ChatConversation $conversation): RedirectResponse|JsonResponse
    {
        $this->authorizeVisitor($request, $conversation);
        abort_if($conversation->status === 'closed', 409, 'This conversation is closed.');
        $validated = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $message = $conversation->messages()->create([
            'sender_type' => 'visitor',
            'body' => $validated['body'],
        ]);
        $conversation->update(['last_message_at' => $message->created_at]);

        if ($request->expectsJson()) {
            return response()->json($this->messageData($message), 201);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function messages(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->authorizeVisitor($request, $conversation);
        $validated = $request->validate(['after' => ['nullable', 'integer', 'min:0']]);

        $messages = $conversation->messages()->where('sender_type', 'lawyer')
            ->where('id', '>', $validated['after'] ?? 0)->oldest()->get();

        return response()->json($messages->map(fn (ChatMessage $message) => $this->messageData($message)));
    }

    private function visitorConversation(Request $request): ?ChatConversation
    {
        $id = $request->session()->get('visitor_chat_id');
        $token = $request->session()->get('visitor_chat_token');

        if (! $id || ! $token) {
            return null;
        }

        $conversation = ChatConversation::query()->find($id);
        if (! $conversation || ! hash_equals($conversation->visitor_token_hash, hash('sha256', $token))) {
            $request->session()->forget(['visitor_chat_id', 'visitor_chat_token']);

            return null;
        }

        return $conversation;
    }

    private function authorizeVisitor(Request $request, ChatConversation $conversation): void
    {
        $owned = $request->session()->get('visitor_chat_id') === $conversation->id
            && is_string($request->session()->get('visitor_chat_token'))
            && hash_equals(
                $conversation->visitor_token_hash,
                hash('sha256', $request->session()->get('visitor_chat_token')),
            );

        abort_unless($owned, 404);
    }

    private function messageData(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'body' => $message->body,
            'created_at' => $message->created_at->toIso8601String(),
        ];
    }
}
