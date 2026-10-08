<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    wire:target="toggle"
    class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors {{ $liked ? 'text-red-600' : 'text-gray-500 hover:text-red-600' }}"
>
    <span>{{ $liked ? '❤️' : '🤍' }}</span>
    <span>{{ $likesCount }}</span>
    <span>{{ $liked ? 'Liked' : 'Like' }}</span>
</button>
