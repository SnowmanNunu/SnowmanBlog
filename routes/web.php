<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GuestbookController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/post/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/series', [SeriesController::class, 'index'])->name('series.index');
Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/rss.xml', [RssController::class, 'index'])->name('rss');

Route::get('/guestbook', [GuestbookController::class, 'index'])->name('guestbook.index');
Route::post('/guestbook', [GuestbookController::class, 'store'])
    ->name('guestbook.store')
    ->middleware('throttle:3,1');
Route::post('/guestbook/{guestbook}/reply', [GuestbookController::class, 'reply'])
    ->name('guestbook.reply');

Route::post('/post/{slug}/like', [BlogController::class, 'like'])->name('blog.like');

Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->name('comments.store')
    ->middleware('throttle:3,1');

Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscribe.store')->middleware('throttle:3,1');
Route::get('/unsubscribe/{token}', [SubscriptionController::class, 'destroy'])->name('subscribe.destroy');

Route::get('/backups/download', function (Request $request) {
    $name = $request->query('name');
    $path = storage_path('app/backups/'.basename($name));
    abort_if(! file_exists($path) || ! auth()->check(), 404);

    return response()->download($path);
})->middleware('auth')->name('backup.download');
