<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Guestbook;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class LatestPendingReviews extends Widget
{
    protected static string $view = 'filament.widgets.latest-pending-reviews';
    protected int | string | array $columnSpan = 'full';

    public function getPendingReviews(): array
    {
        $comments = DB::table('comments')
            ->select('id', 'nickname', 'content', 'post_id', 'created_at', DB::raw("'评论' as type"))
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        $guestbooks = DB::table('guestbooks')
            ->select('id', 'nickname', 'content', DB::raw('NULL as post_id'), 'created_at', DB::raw("'留言' as type"))
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        $merged = array_merge($comments, $guestbooks);
        usort($merged, fn ($a, $b) => $b->created_at <=> $a->created_at);

        return array_slice($merged, 0, 10);
    }

    public function getPostTitle(?int $postId): ?string
    {
        if (!$postId) return null;
        return DB::table('posts')->where('id', $postId)->value('title');
    }

    public function approve(int $id, string $type): void
    {
        if ($type === '评论') {
            Comment::where('id', $id)->update(['is_approved' => true]);
        } else {
            Guestbook::where('id', $id)->update(['is_approved' => true]);
        }

        Notification::make()
            ->success()
            ->title('已通过')
            ->send();
    }

    public function deleteReview(int $id, string $type): void
    {
        if ($type === '评论') {
            Comment::where('id', $id)->delete();
        } else {
            Guestbook::where('id', $id)->delete();
        }

        Notification::make()
            ->success()
            ->title('已删除')
            ->send();
    }
}
