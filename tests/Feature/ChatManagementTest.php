<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_start_a_conversation_and_send_a_message(): void
    {
        $this->post(route('chat.start'), [
            'visitor_name' => 'Jordan Visitor',
            'visitor_email' => 'jordan@example.test',
            'body' => 'I need some information.',
        ])->assertRedirect();

        $conversation = ChatConversation::query()->firstOrFail();
        $this->assertSame('open', $conversation->status);
        $this->assertDatabaseHas('chat_messages', [
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'visitor',
            'body' => 'I need some information.',
        ]);
        $this->assertNotEmpty(session('visitor_chat_token'));

        $this->post(route('chat.messages.store', $conversation), ['body' => 'A follow-up question.'])
            ->assertRedirect(route('chat.show', $conversation));
        $this->assertDatabaseHas('chat_messages', [
            'chat_conversation_id' => $conversation->id,
            'body' => 'A follow-up question.',
        ]);
    }

    public function test_floating_widget_uses_the_visitors_secure_session(): void
    {
        $this->getJson(route('chat.widget'))->assertOk()->assertJsonPath('conversation', null);

        $this->postJson(route('chat.start'), [
            'visitor_name' => 'Floating Visitor',
            'visitor_email' => '',
            'body' => 'Hello from the floating chat.',
        ])->assertCreated()->assertJsonPath('conversation.name', 'Floating Visitor');

        $this->getJson(route('chat.widget'))
            ->assertOk()
            ->assertJsonPath('conversation.name', 'Floating Visitor')
            ->assertJsonPath('conversation.messages.0.body', 'Hello from the floating chat.');
    }

    public function test_visitor_cannot_access_another_session_conversation(): void
    {
        $token = 'visitor-session-secret';
        $conversation = ChatConversation::query()->create([
            'visitor_name' => 'Private Visitor',
            'visitor_token_hash' => hash('sha256', $token),
            'status' => 'open',
        ]);
        $conversation->messages()->create(['sender_type' => 'visitor', 'body' => 'Private message.']);

        $this->withSession([
            'visitor_chat_id' => $conversation->id,
            'visitor_chat_token' => $token,
        ])->get(route('chat.show', $conversation))->assertOk()->assertSee('Private message.');

        $this->withSession(['visitor_chat_id' => null, 'visitor_chat_token' => null])
            ->get(route('chat.show', $conversation))->assertNotFound();
        $this->withSession(['visitor_chat_id' => null, 'visitor_chat_token' => null])
            ->post(route('chat.messages.store', $conversation), ['body' => 'Unauthorized message.'])->assertNotFound();
    }

    public function test_lawyer_can_manage_reply_to_and_close_a_conversation(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        $conversation = ChatConversation::query()->create([
            'visitor_name' => 'Website Visitor',
            'visitor_token_hash' => hash('sha256', 'visitor-token'),
            'status' => 'open',
        ]);
        $visitorMessage = $conversation->messages()->create(['sender_type' => 'visitor', 'body' => 'Hello lawyer.']);

        $this->actingAs($lawyer)->get(route('admin.chats.index'))->assertOk()->assertSee('Website Visitor');
        $this->get(route('admin.chats.show', $conversation))->assertOk()->assertSee('Hello lawyer.');
        $this->assertNotNull($visitorMessage->fresh()->read_at);

        $this->postJson(route('admin.chats.reply', $conversation), ['body' => 'Thank you for contacting me.'])
            ->assertCreated()->assertJsonPath('sender_type', 'lawyer');
        $this->patchJson(route('admin.chats.status', $conversation), ['status' => 'closed'])
            ->assertOk()->assertJsonPath('status', 'closed');
        $this->assertDatabaseHas('chat_conversations', ['id' => $conversation->id, 'status' => 'closed']);
    }

    public function test_non_lawyer_cannot_open_chat_management(): void
    {
        $user = User::factory()->create(['is_lawyer' => false]);

        $this->actingAs($user)->get(route('admin.chats.index'))->assertForbidden();
    }
}
