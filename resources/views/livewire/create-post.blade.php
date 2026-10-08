<div class="bg-white shadow rounded-lg p-4">
    <form wire:submit="save" class="space-y-3">
        <div>
            <textarea
                wire:model="body"
                rows="3"
                placeholder="What's happening?"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            ></textarea>
            <x-input-error :messages="$errors->get('body')" class="mt-1" />
        </div>

        <div>
            <input
                type="file"
                wire:model="image"
                accept="image/*"
                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-gray-100 file:text-sm file:font-semibold hover:file:bg-gray-200"
            >
            <x-input-error :messages="$errors->get('image')" class="mt-1" />

            <div wire:loading wire:target="image" class="text-sm text-gray-400 mt-1">Uploading…</div>

            @if ($image)
                <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="mt-2 h-32 rounded-md object-cover">
            @endif
        </div>

        <div class="flex justify-end">
            <x-primary-button type="submit" wire:loading.attr="disabled" wire:target="save,image">
                Post
            </x-primary-button>
        </div>
    </form>
</div>
