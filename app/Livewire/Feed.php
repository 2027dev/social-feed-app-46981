<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Attributes\On;
use Livewire\Component;

class Feed extends Component
{
    public int $perPage = 10;

    #[On('post-created')]
    public function postCreated(): void
    {
        $this->perPage = max($this->perPage, 10);
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function deletePost(int $postId): void
    {
        $post = Post::findOrFail($postId);

        $this->authorize('delete', $post);

        $post->delete();
    }

    public function render()
    {
        $posts = Post::with(['user', 'likes'])
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.feed', ['posts' => $posts]);
    }
}
