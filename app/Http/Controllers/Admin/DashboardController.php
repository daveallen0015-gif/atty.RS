<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\Inquiry;
use App\Models\PageContent;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'unreadInquiryCount' => Inquiry::query()->where('status', 'new')->count(),
            'inquiryCount' => Inquiry::query()->count(),
            'activeConversationCount' => ChatConversation::query()->where('status', 'open')->count(),
            'serviceCount' => Service::query()->count(),
            'recentInquiries' => Inquiry::query()->latest()->limit(5)->get(),
            'recentConversations' => ChatConversation::query()->with('latestMessage')
                ->withCount('unreadVisitorMessages')->latest('last_message_at')->limit(5)->get(),
            'recentUpdates' => PageContent::query()->whereNotNull('updated_by')->latest('updated_at')->limit(5)->get(),
        ]);
    }
}
