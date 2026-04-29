<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Guestbook;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_posts(): void
    {
        Post::factory()->count(5)->create();

        $response = $this->get(route('blog.index'));

        $response->assertStatus(200);
        $response->assertViewHas('posts');
    }

    public function test_post_detail_page(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertStatus(200);
        $response->assertViewHas('post', $post);
    }

    public function test_category_filter_page(): void
    {
        $category = Category::factory()->create();
        Post::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->get(route('blog.category', $category->slug));

        $response->assertStatus(200);
        $response->assertViewHas('category', $category);
    }

    public function test_tag_filter_page(): void
    {
        $tag = Tag::factory()->create();
        $posts = Post::factory()->count(3)->create();
        $tag->posts()->attach($posts);

        $response = $this->get(route('blog.tag', $tag->slug));

        $response->assertStatus(200);
        $response->assertViewHas('tag', $tag);
    }

    public function test_search_page(): void
    {
        Post::factory()->create(['title' => 'Laravel Tutorial']);

        $response = $this->get(route('blog.search', ['q' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertViewHas('posts');
    }

    public function test_guestbook_page_displays_messages(): void
    {
        Guestbook::factory()->count(3)->create();

        $response = $this->get(route('guestbook.index'));

        $response->assertStatus(200);
        $response->assertViewHas('messages');
    }

    public function test_guestbook_store(): void
    {
        $data = [
            'nickname' => 'TestUser',
            'email' => 'test@example.com',
            'content' => 'This is a test message.',
        ];

        $response = $this->post(route('guestbook.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('guestbooks', [
            'nickname' => 'TestUser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_comment_store(): void
    {
        $post = Post::factory()->create();

        $data = [
            'nickname' => 'Commenter',
            'email' => 'commenter@example.com',
            'content' => 'Nice post!',
        ];

        $response = $this->post(route('comments.store', $post), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'nickname' => 'Commenter',
        ]);
    }

    public function test_sitemap_xml(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->get(route('sitemap'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function test_rss_xml(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->get(route('rss'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function test_post_like(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(route('blog.like', $post->slug));

        $response->assertOk();
        $this->assertDatabaseHas('post_likes', [
            'post_id' => $post->id,
        ]);
    }
}
