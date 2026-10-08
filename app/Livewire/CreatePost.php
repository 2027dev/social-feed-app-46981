<?php

namespace App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePost extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:2000')]
    public string $body = '';

    #[Validate('required|image|max:5120')]
    public $image;

    public function save(): void
    {
        $this->validate();

        $path = $this->image->store('posts', 'public');

        auth()->user()->posts()->create([
            'body' => $this->body,
            'image_path' => $path,
        ]);

        $this->reset(['body', 'image']);

        $this->dispatch('post-created');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
