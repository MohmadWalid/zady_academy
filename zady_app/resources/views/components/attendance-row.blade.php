@props(['student', 'present', 'groupId', 'date'])

<div x-data="{ 
        present: {{ $present ? 'true' : 'false' }},
        flash: false,
        toggle() {
            this.present = !this.present;
            this.flash = true;
            setTimeout(() => this.flash = false, 150);
            // In a real app, you would make an AJAX request here
            // fetch(`/api/attendance`, { method: 'POST', body: JSON.stringify({ student_id: {{ $student->id }}, present: this.present, date: '{{ $date }}' }) })
        }
    }" 
    @click="toggle()"
    :class="{'bg-primary-light': flash, 'bg-surface': !flash}"
    class="flex items-center justify-between px-4 h-[64px] border-b border-border transition-colors cursor-pointer active:bg-primary-light touch-manipulation">
    
    <div class="flex items-center gap-3 pointer-events-none">
        <div class="w-10 h-10 rounded-full bg-bg flex items-center justify-center text-text-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
            <h4 class="font-medium text-text-primary text-sm">{{ $student->name }}</h4>
            <span class="text-xs text-text-secondary">{{ $student->access_code }}</span>
        </div>
    </div>

    <div class="pointer-events-none">
        <div :class="{'bg-success': present, 'bg-border': !present}" 
             class="w-[52px] h-[30px] rounded-full relative transition-colors duration-200">
            <div :class="{'translate-x-[-22px]': present, 'translate-x-0': !present}" 
                 class="absolute right-1 top-1 w-[22px] h-[22px] bg-white rounded-full shadow-sm transition-transform duration-200"></div>
        </div>
    </div>
</div>
