<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GuestbookController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\RssController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/post/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
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

