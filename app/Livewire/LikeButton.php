<?php

namespace App\Livewire;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Component;

class LikeButton extends Component
{
    public Post $post;

    public int $likesCount;

    public bool $liked;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->refreshState();
    }

    public function toggle(): void
    {
        $userId = auth()->id();

        $deleted = Like::query()
            ->where('post_id', $this->post->id)
            ->where('user_id', $userId)
            ->delete();

        if (! $deleted) {
            try {
                Like::create([
                    'post_id' => $this->post->id,
                    'user_id' => $userId,
                ]);
            } catch (UniqueConstraintViolationException) {
                // Already liked in a concurrent request; nothing to do.
            }
        }

        $this->refreshState();
    }

    private function refreshState(): void
    {
        $this->likesCount = Like::where('post_id', $this->post->id)->count();
        $this->liked = Like::where('post_id', $this->post->id)
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function render()
    {
        return view('livewire.like-button');
    }
}
