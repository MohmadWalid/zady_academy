@extends('layouts.app')

@section('title', 'لوحة المعلم')

@section('content')
<div class="mb-8">
    <x-page-header title="مرحباً، أ. {{ auth()->user()->name }}" subtitle="إليك نظرة عامة على حلقاتك اليوم" />
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <x-stat-card label="إجمالي الطلاب" value="{{ $totalStudents }}" />
    <x-stat-card label="حلقات تم تحضيرها اليوم" value="{{ count($attendanceDoneToday) }}" />
    <x-stat-card label="حلقات متبقية للتحضير" value="{{ $groupsNeedingAttendance }}" />
</div>

<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-text-primary">الحلقات المكلف بها</h3>
    <div class="h-1 flex-1 mx-6 bg-border/30 rounded-full hidden md:block"></div>
    <span class="text-sm font-medium text-text-secondary">{{ $groups->count() }} حلقات</span>
</div>

@if($groups->isEmpty())
    <x-card>
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
            title="لا توجد حلقات حالياً" 
            subtitle="لم يتم تكليفك بأي حلقات قرآنية بعد. تواصل مع الإدارة." />
    </x-card>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($groups as $group)
            @php
                $isDoneToday = in_array($group->id, $attendanceDoneToday);
            @endphp
            <div class="relative">
                <a href="{{ route('teacher.attendance.show', $group) }}" class="block group">
                    <x-card class="h-full border-2 {{ $isDoneToday ? 'border-success/20 bg-success-bg/5' : 'border-transparent hover:border-primary/20' }} transition-all duration-300">
                        <div class="flex justify-between items-start mb-6">
                            <div class="p-3 rounded-2xl {{ $isDoneToday ? 'bg-success/10 text-success' : 'bg-primary/10 text-primary' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            @if($isDoneToday)
                                <span class="px-3 py-1 rounded-full bg-success text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">تم التحضير</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-warning text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">بانتظار التحضير</span>
                            @endif
                        </div>

                        <h4 class="text-xl font-bold text-text-primary mb-2 group-hover:text-primary transition-colors">{{ $group->name }}</h4>
                        <div class="flex items-center gap-2 text-sm text-text-secondary mb-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span>{{ $group->active_enrollments_count }} طالب مسجل</span>
                        </div>

                        <div class="pt-6 border-t border-border flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-text-secondary uppercase font-bold tracking-widest">الموعد القادم</span>
                                <span class="text-sm font-bold text-text-primary">
                                    @php $next = $group->sessions->first(); @endphp
                                    {{ $next ? match($next->day) { 'Saturday' => 'السبت', 'Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', default => $next->day } : 'غير محدد' }}
                                </span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-bg flex items-center justify-center text-text-secondary group-hover:bg-primary group-hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    </x-card>
                </a>
            </div>
        @endforeach
    </div>
@endif
@endsection
