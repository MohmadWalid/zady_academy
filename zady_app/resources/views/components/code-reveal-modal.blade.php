@props(['code'])

<div x-data="{ open: false, copied: false }" 
     x-show="open" 
     x-on:open-code-modal.window="open = true"
     class="fixed inset-0 z-50 flex items-center justify-center" 
     style="display: none;">
    
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/40" @click="open = false"></div>

    <!-- Modal -->
    <div class="relative bg-surface rounded-2xl p-6 w-full max-w-[340px] shadow-xl z-10 flex flex-col items-center text-center">
        
        <div class="w-10 h-10 bg-primary-light text-primary rounded-full flex items-center justify-center mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </div>

        <h3 class="text-lg font-bold text-text-primary mb-4">كود الدخول الجديد</h3>

        <div class="bg-bg border border-border rounded-xl px-4 py-3 mb-6 w-full flex justify-center items-center">
            <span class="text-2xl font-mono text-primary font-bold" id="access-code-{{ $code }}">{{ $code }}</span>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <button @click="navigator.clipboard.writeText('{{ $code }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })" 
                    class="w-full py-3 rounded-xl font-medium text-white bg-primary hover:bg-primary-dark transition-colors flex justify-center items-center gap-2">
                <span x-text="copied ? 'تم النسخ!' : 'نسخ الكود'"></span>
                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <svg x-show="copied" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </button>
            <button @click="open = false" class="w-full py-3 rounded-xl font-medium text-text-secondary hover:bg-bg transition-colors">
                إغلاق
            </button>
        </div>
    </div>
</div>
