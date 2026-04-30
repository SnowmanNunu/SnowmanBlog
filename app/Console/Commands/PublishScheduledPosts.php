<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publish scheduled posts that have reached their publish time';

    public function handle(): void
    {
        $posts = Post::where('is_published', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $post->update(['is_published' => true]);
            $this->info("Published: {$post->title}");
        }

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts to publish.');
        } else {
            $this->info("Published {$posts->count()} scheduled post(s).");
        }
    }
}
