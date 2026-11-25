<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['status' => ['nullable', 'in:open,closed']]);
        $conversations = ChatConversation::query()->with('latestMessage')
            ->withCount('unreadVisitorMessages')
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('last_message_at')->paginate(15)->withQueryString();

        return view('admin.chats.index', [
            'conversations' => $conversations,
            'selectedStatus' => $validated['status'] ?? '',
        ]);
    }

    public function show(ChatConversation $conversation): View
    {
        $conversation->messages()->where('sender_type', 'visitor')->whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.chats.show', [
            'conversation' => $conversation->load('messages'),
        ]);
    }

    public function messages(Request $request, ChatConversation $conversation): JsonResponse
    {
        $validated = $request->validate(['after' => ['nullable', 'integer', 'min:0']]);
        $messages = $conversation->messages()->where('sender_type', 'visitor')
            ->where('id', '>', $validated['after'] ?? 0)->oldest()->get();
        $conversation->messages()->whereIn('id', $messages->pluck('id'))->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json($messages->map(fn (ChatMessage $message) => $this->messageData($message)));
    }

    public function reply(Request $request, ChatConversation $conversation): JsonResponse|RedirectResponse
    {
        abort_if($conversation->status === 'closed', 409, 'This conversation is closed.');
        $validated = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $message = $conversation->messages()->create([
            'sender_type' => 'lawyer',
            'body' => $validated['body'],
            'read_at' => now(),
        ]);
        $conversation->update(['last_message_at' => $message->created_at]);

        if ($request->expectsJson()) {
            return response()->json($this->messageData($message), 201);
        }

        return redirect()->route('admin.chats.show', $conversation);
    }

    public function updateStatus(Request $request, ChatConversation $conversation): JsonResponse|RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', 'in:open,closed']]);
        $conversation->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['status' => $conversation->status]);
        }

        return redirect()->route('admin.chats.show', $conversation)->with('status', 'Conversation status updated.');
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
