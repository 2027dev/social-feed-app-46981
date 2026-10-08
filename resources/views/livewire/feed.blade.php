<div class="max-w-xl mx-auto py-6 px-4 space-y-6">
    <livewire:create-post />

    <div class="space-y-6">
        @forelse ($posts as $post)
            <div class="bg-white shadow rounded-lg p-4" wire:key="post-{{ $post->id }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-800">{{ $post->user->name }}</span>
                    <span class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                </div>

                <p class="text-gray-700 mb-3 whitespace-pre-line">{{ $post->body }}</p>

                <img
                    src="{{ $post->imageUrl() }}"
                    alt="Post image"
                    loading="lazy"
                    class="rounded-lg mb-3 w-full object-cover max-h-96 bg-gray-100"
                >

                <div class="flex items-center justify-between">
                    <livewire:like-button :post="$post" :key="'like-'.$post->id" />

                    @can('delete', $post)
                        <button
                            wire:click="deletePost({{ $post->id }})"
                            wire:confirm="Delete this post? This cannot be undone."
                            class="text-sm text-red-500 hover:text-red-700 hover:underline"
                        >
                            Delete
                        </button>
                    @endcan
                </div>
            </div>
        @empty
            <p class="text-center text-gray-400 py-12">No posts yet. Be the first to share something!</p>
        @endforelse
    </div>

    @if ($posts->hasMorePages())
        <div wire:intersect="loadMore" class="text-center text-gray-400 py-4">
            Loading more…
        </div>
    @endif
</div>
