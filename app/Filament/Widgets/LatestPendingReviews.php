<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Guestbook;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class LatestPendingReviews extends Widget implements HasActions
{
    use InteractsWithActions;

    protected static string $view = 'filament.widgets.latest-pending-reviews';

    protected int|string|array $columnSpan = 'full';

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

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
        if (! $postId) {
            return null;
        }

        return DB::table('posts')->where('id', $postId)->value('title');
    }

    public function approveAction(): Action
    {
        return Action::make('approve')
            ->requiresConfirmation()
            ->modalHeading('确认通过')
            ->modalDescription(fn (array $arguments): string => "确定要通过 {$arguments['nickname']} 的{$arguments['type']}吗？")
            ->modalSubmitActionLabel('确认通过')
            ->color('success')
            ->action(function (array $arguments) {
                if ($arguments['type'] === '评论') {
                    Comment::where('id', $arguments['id'])->update(['is_approved' => true]);
                } else {
                    Guestbook::where('id', $arguments['id'])->update(['is_approved' => true]);
                }
                Notification::make()->success()->title('已通过')->send();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalHeading('确认删除')
            ->modalDescription(fn (array $arguments): string => "确定要删除 {$arguments['nickname']} 的{$arguments['type']}吗？")
            ->modalSubmitActionLabel('确认删除')
            ->color('danger')
            ->action(function (array $arguments) {
                if ($arguments['type'] === '评论') {
                    Comment::where('id', $arguments['id'])->delete();
                } else {
                    Guestbook::where('id', $arguments['id'])->delete();
                }
                Notification::make()->success()->title('已删除')->send();
            });
    }
}
