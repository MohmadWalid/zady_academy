@props(['id', 'title', 'body', 'confirmLabel', 'isDanger' => false])

<div x-data="{ open: false }" 
     x-show="open" 
     @keydown.escape.window="open = false"
     x-on:open-dialog-{{ $id }}.window="open = true"
     x-on:close-dialog-{{ $id }}.window="open = false"
     class="fixed inset-0 z-50 flex items-center justify-center" 
     style="display: none;">
    
    <!-- Overlay -->
    <div x-show="open" 
         x-transition.opacity 
         class="fixed inset-0 bg-black/40" 
         @click="open = false"></div>

    <!-- Dialog -->
    <div x-show="open" 
         x-transition.scale.origin.center 
         class="relative bg-surface rounded-2xl p-6 w-full max-w-[340px] shadow-xl z-10 flex flex-col gap-4">
        
        <div class="text-center">
            <h3 class="text-lg font-bold text-text-primary mb-2">{{ $title }}</h3>
            <p class="text-sm text-text-secondary">{{ $body }}</p>
        </div>

        <div class="flex flex-col gap-2 mt-2">
            <button @click="$dispatch('confirm-{{ $id }}'); open = false" 
                    class="w-full py-3 rounded-xl font-medium text-white transition-colors {{ $isDanger ? 'bg-danger hover:bg-red-700' : 'bg-primary hover:bg-primary-dark' }}">
                {{ $confirmLabel }}
            </button>
            <button @click="open = false" 
                    class="w-full py-3 rounded-xl font-medium text-text-secondary hover:bg-bg transition-colors">
                إلغاء
            </button>
        </div>
    </div>
</div>
