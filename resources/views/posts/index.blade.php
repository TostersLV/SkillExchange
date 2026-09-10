<x-layouts::app title="Home">
    <div class="mx-auto w-full max-w-5xl p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Skill exchange posts</flux:heading>
                <flux:text class="mt-2">Browse skills people are offering and what they want in return.</flux:text>
            </div>
        </div>

        <div class="mt-8">
            <livewire:posts.search-and-filter />
        </div>
    </div>
</x-layouts::app>
