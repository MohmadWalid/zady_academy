@props(['icon', 'title', 'subtitle', 'actionLabel' => null, 'actionRoute' => null])

<div class="flex flex-col items-center justify-center p-8 text-center bg-surface rounded-[14px] border border-border">
    <div class="text-border mb-4">
        {!! $icon !!}
    </div>
    <h3 class="text-text-primary text-lg font-bold mb-2">{{ $title }}</h3>
    <p class="text-text-secondary text-sm mb-6 max-w-sm">{{ $subtitle }}</p>
    
    @if($actionLabel && $actionRoute)
        <a href="{{ $actionRoute }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white rounded-lg font-medium text-sm hover:bg-primary-dark transition-colors">
            {{ $actionLabel }}
        </a>
    @endif
</div>
