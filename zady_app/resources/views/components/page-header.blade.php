@props(['title', 'subtitle' => null])

<div class="mb-6 flex flex-col gap-1">
    <h1 class="text-2xl sm:text-3xl font-bold text-text-primary">{{ $title }}</h1>
    @if($subtitle)
        <p class="text-text-secondary text-sm sm:text-base">{{ $subtitle }}</p>
    @endif
</div>
