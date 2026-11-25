<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\LawController as AdminLawController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Public\ChatController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LawController;
use Illuminate\Support\Facades\Route;
use App\Services\GitHubImageService;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/services/{service:slug}', [HomeController::class, 'service'])->name('services.show');
Route::get('/philippine-laws', [LawController::class, 'index'])->name('laws.index');
Route::get('/philippine-laws/{law:slug}', [LawController::class, 'show'])->name('laws.show');
Route::redirect('/practice-areas', '/services')->name('practice-areas');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/widget', [ChatController::class, 'widget'])->name('chat.widget');
Route::post('/chat/conversations', [ChatController::class, 'start'])->middleware('throttle:5,1')->name('chat.start');
Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
Route::get('/chat/{conversation}/messages', [ChatController::class, 'messages'])->name('chat.messages');
Route::post('/chat/{conversation}/messages', [ChatController::class, 'send'])->middleware('throttle:10,1')->name('chat.messages.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware(['auth', 'lawyer'])->group(function () {
        Route::get('/github-media-diagnostic', fn (GitHubImageService $images) => response()->json($images->diagnostic()))->name('github-media.diagnostic');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::resource('/services', ServiceController::class)->except('show');
        Route::resource('/laws', AdminLawController::class)->except('show');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
        Route::patch('/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
        Route::get('/chats', [AdminChatController::class, 'index'])->name('chats.index');
        Route::get('/chats/{conversation}', [AdminChatController::class, 'show'])->name('chats.show');
        Route::get('/chats/{conversation}/messages', [AdminChatController::class, 'messages'])->name('chats.messages');
        Route::post('/chats/{conversation}/messages', [AdminChatController::class, 'reply'])->name('chats.reply');
        Route::patch('/chats/{conversation}/status', [AdminChatController::class, 'updateStatus'])->name('chats.status');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
