<?php

use AppHttpControllersBlogController;
use AppHttpControllersCommentController;
use AppHttpControllersGuestbookController;
use AppHttpControllersSitemapController;
use AppHttpControllersRssController;
use IlluminateSupportFacadesRoute;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/post/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/rss.xml', [RssController::class, 'index'])->name('rss');

Route::get('/guestbook', [GuestbookController::class, 'index'])->name('guestbook.index');
Route::post('/guestbook', [GuestbookController::class, 'store'])->name('guestbook.store');

Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');