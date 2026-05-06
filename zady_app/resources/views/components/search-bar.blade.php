@props(['name' => 'search', 'placeholder' => 'بحث...'])

<div class="relative w-full">
    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-text-secondary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>
    <input type="search" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ request($name) }}" class="block w-full pl-4 pr-11 h-[48px] border border-border rounded-[24px] bg-surface text-sm focus:ring-primary focus:border-primary placeholder:text-text-disabled">
</div>
