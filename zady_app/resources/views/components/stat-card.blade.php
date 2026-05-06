@props(['value', 'label'])

<div class="bg-surface rounded-[14px] p-5 shadow-sm border border-border flex flex-col justify-center">
    <div class="text-text-secondary text-xs font-medium mb-1">{{ $label }}</div>
    <div class="text-text-primary text-2xl font-bold">{{ $value }}</div>
</div>
