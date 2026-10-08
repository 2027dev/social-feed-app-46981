<?php

namespace Tests\Feature;

use App\Livewire\CreatePost;
use App\Livewire\Feed;
use App\Livewire\LikeButton;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_post_with_an_image_stored_on_the_public_disk(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreatePost::class)
            ->set('body', 'Hello world')
            ->set('image', UploadedFile::fake()->image('photo.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::sole();

        $this->assertSame('Hello world', $post->body);
        $this->assertSame($user->id, $post->user_id);
        Storage::disk('public')->assertExists($post->image_path);
    }

    public function test_feed_shows_posts_reverse_chronologically_with_pagination(): void
    {
        $user = User::factory()->create();
        $older = Post::factory()->for($user)->create(['created_at' => now()->subDay()]);
        $newer = Post::factory()->for($user)->create(['created_at' => now()]);

        Livewire::actingAs($user)
            ->test(Feed::class)
            ->assertSeeInOrder([$newer->body, $older->body]);
    }

    public function test_load_more_reveals_additional_posts(): void
    {
        $user = User::factory()->create();
        Post::factory()->for($user)->count(15)->create();

        Livewire::actingAs($user)
            ->test(Feed::class)
            ->assertViewHas('posts', fn ($posts) => $posts->count() === 10)
            ->call('loadMore')
            ->assertViewHas('posts', fn ($posts) => $posts->count() === 15);
    }

    public function test_user_can_like_and_unlike_a_post_with_live_count(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $component = Livewire::actingAs($user)
            ->test(LikeButton::class, ['post' => $post])
            ->assertSet('likesCount', 0)
            ->assertSet('liked', false);

        $component->call('toggle')
            ->assertSet('likesCount', 1)
            ->assertSet('liked', true);

        $this->assertDatabaseHas('likes', ['post_id' => $post->id, 'user_id' => $user->id]);

        $component->call('toggle')
            ->assertSet('likesCount', 0)
            ->assertSet('liked', false);

        $this->assertDatabaseMissing('likes', ['post_id' => $post->id, 'user_id' => $user->id]);
    }

    public function test_owner_can_delete_their_own_post_and_its_image_is_removed(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreatePost::class)
            ->set('body', 'Delete me')
            ->set('image', UploadedFile::fake()->image('photo.jpg'))
            ->call('save');

        $post = Post::sole();
        $imagePath = $post->image_path;
        Storage::disk('public')->assertExists($imagePath);

        Livewire::actingAs($user)
            ->test(Feed::class)
            ->call('deletePost', $post->id);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_user_cannot_delete_another_users_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->create();

        Livewire::actingAs($other)
            ->test(Feed::class)
            ->call('deletePost', $post->id)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }
}
