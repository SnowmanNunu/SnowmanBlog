<?php

namespace App\Console\Commands;

use App\Mail\NewPostNotification;
use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publish scheduled posts that have reached their publish time and notify subscribers';

    public function handle(): void
    {
        $posts = Post::where('is_published', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        $subscribers = Subscriber::verified()->get();

        foreach ($posts as $post) {
            $post->update(['is_published' => true]);
            $this->info("Published: {$post->title}");

            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new NewPostNotification($post, $subscriber));
            }
        }

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts to publish.');
        } else {
            $this->info("Published {$posts->count()} scheduled post(s).");
            if ($subscribers->isNotEmpty()) {
                $this->info("Queued notifications for {$subscribers->count()} subscriber(s).");
            }
        }
    }
}
