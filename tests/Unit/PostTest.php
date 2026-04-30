<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_excludes_drafts(): void
    {
        Post::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);
        Post::factory()->create(['is_published' => false, 'published_at' => null]);
        Post::factory()->create(['is_published' => true, 'published_at' => now()->addDay()]);

        $published = Post::published()->get();

        $this->assertCount(1, $published);
    }

    public function test_post_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create(['category_id' => $category->id]);
        $post->load('category');

        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertEquals($category->id, $post->category->id);
    }

    public function test_post_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $post->load('user');

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }

    public function test_post_has_many_tags(): void
    {
        $post = Post::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $post->tags()->attach($tags);

        $this->assertCount(3, $post->fresh()->tags);
    }

    public function test_post_has_many_comments(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(3)->create(['post_id' => $post->id]);

        $this->assertCount(3, $post->fresh()->comments);
    }

    public function test_pinned_posts_can_be_queried(): void
    {
        Post::factory()->create(['is_pinned' => true]);
        Post::factory()->create(['is_pinned' => false]);

        $pinned = Post::where('is_pinned', true)->get();

        $this->assertCount(1, $pinned);
    }
}
