@props(['group', 'studentCount'])

<div class="bg-surface rounded-[14px] p-4 shadow-sm border border-border border-r-[3px] border-r-primary flex flex-col gap-3">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="text-text-primary font-semibold text-lg">{{ $group->name }}</h3>
            <p class="text-text-secondary text-sm mt-1">المعلم: {{ $group->teacher->name ?? 'غير محدد' }}</p>
        </div>
        <div class="flex items-center gap-1 text-text-secondary bg-bg px-2 py-1 rounded-lg text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span>{{ $studentCount }}</span>
        </div>
    </div>
    <div class="flex items-center text-sm text-text-secondary">
        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>{{ $group->schedule_day ?? 'لم يحدد' }} - {{ $group->schedule_time ?? 'لم يحدد' }}</span>
    </div>
</div>
