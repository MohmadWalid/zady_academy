@extends('layouts.app')

@section('title', 'تحضير الحلقة')

@section('content')
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('teacher.attendance.index', ['date' => $date]) }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </a>
    <x-page-header title="{{ $group->name }}" subtitle="تحضير الطلاب ليوم {{ $date }}" />
</div>

<form action="{{ route('teacher.attendance.store', $group) }}" method="POST" id="attendance-form">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    
    <x-card class="p-0 overflow-hidden mb-24">
        <div class="divide-y divide-border">
            @forelse($group->activeEnrollments as $index => $enrollment)
                @php
                    $isPresent = isset($existingAttendance[$enrollment->student_id]) ? $existingAttendance[$enrollment->student_id] : true;
                @endphp
                <div class="attendance-row relative">
                    <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $enrollment->student_id }}">
                    <input type="checkbox" 
                           name="attendance[{{ $index }}][present]" 
                           value="1" 
                           id="student-{{ $enrollment->student_id }}"
                           class="peer hidden"
                           {{ $isPresent ? 'checked' : '' }}>
                    
                    <label for="student-{{ $enrollment->student_id }}" 
                           class="flex items-center justify-between p-5 cursor-pointer transition-all peer-checked:bg-success-bg/10 hover:bg-bg select-none">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold text-lg">
                                {{ mb_substr($enrollment->student->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-text-primary">{{ $enrollment->student->name }}</h4>
                                <p class="text-xs text-text-secondary">كود الطالب: ZADY-{{ str_pad($enrollment->student_id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        
                        <div class="status-indicator flex items-center gap-2">
                            <span class="present-label hidden peer-checked:inline-flex items-center px-4 py-1.5 rounded-full bg-success text-white text-sm font-bold">حاضر</span>
                            <span class="absent-label inline-flex peer-checked:hidden items-center px-4 py-1.5 rounded-full bg-error text-white text-sm font-bold">غائب</span>
                            
                            <div class="w-6 h-6 rounded-full border-2 border-border flex items-center justify-center peer-checked:border-success peer-checked:bg-success">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    </label>
                </div>
            @empty
                <div class="p-12 text-center text-text-secondary">
                    لا يوجد طلاب مسجلين في هذه الحلقة حالياً.
                </div>
            @endforelse
        </div>
    </x-card>

    <div class="fixed bottom-0 left-0 right-0 p-4 bg-surface border-t border-border z-40 flex justify-center">
        <button type="submit" class="w-full max-w-lg h-14 bg-primary text-white text-lg font-bold rounded-2xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
            حفظ سجل التحضير
        </button>
    </div>
</form>

<style>
    /* Ensure the labels react to the hidden checkbox being checked */
    input[type="checkbox"]:checked ~ label .present-label { display: inline-flex; }
    input[type="checkbox"]:not(:checked) ~ label .present-label { display: none; }
    input[type="checkbox"]:checked ~ label .absent-label { display: none; }
    input[type="checkbox"]:not(:checked) ~ label .absent-label { display: inline-flex; }
    
    input[type="checkbox"]:checked ~ label .status-indicator > div {
        border-color: #10B981;
        background-color: #10B981;
    }
</style>

<script>
    // Since we're using a hidden checkbox, we need to handle the case where it's NOT checked (Laravel doesn't submit unchecked checkboxes).
    // Actually, I'll use a hidden input field that changes when checkbox changes, or just a simple trick.
    // Better: use the hidden input field approach.
    document.getElementById('attendance-form').addEventListener('submit', function(e) {
        // Find all checkboxes and ensure we send 0 for unchecked ones
        const checkboxes = this.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            if (!cb.checked) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = cb.name;
                hidden.value = '0';
                this.appendChild(hidden);
            }
        });
    });
</script>
@endsection
