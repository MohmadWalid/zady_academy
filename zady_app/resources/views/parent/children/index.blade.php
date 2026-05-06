@extends('layouts.app')

@section('title', 'أبنائي')

@section('content')
<x-page-header title="أبنائي" subtitle="عرض جميع الطلاب المسجلين تحت حسابك" />

@if($children->isEmpty())
    <x-card>
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
            title="لا يوجد أبناء مسجلين" 
            subtitle="لم يتم ربط أي طلاب بحسابك بعد. يرجى مراجعة إدارة الأكاديمية." />
    </x-card>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($children as $child)
            <x-card class="relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-2 h-full bg-primary-light group-hover:bg-primary transition-colors"></div>
                
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-primary-light text-primary rounded-2xl flex items-center justify-center font-bold text-3xl shadow-inner">
                            {{ mb_substr($child->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-2xl text-text-primary">{{ $child->name }}</h3>
                            <p class="text-sm text-text-secondary">كود الطالب: ZADY-{{ str_pad($child->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <x-status-badge status="active" />
                        <span class="text-xs text-text-secondary">العمر: {{ $child->age }} سنوات</span>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-3">الحلقات النشطة</h4>
                        <div class="flex flex-col gap-3">
                            @forelse($child->activeEnrollments as $enrollment)
                                <div class="bg-bg/50 p-4 rounded-xl border border-border hover:border-primary/20 transition-colors">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-bold text-text-primary">{{ $enrollment->group->name }}</span>
                                        <span class="text-primary font-bold text-sm">{{ number_format($enrollment->group->monthly_price) }} جنيه</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-text-secondary">
                                        <span>المعلم: {{ $enrollment->group->teacher->name ?? 'غير محدد' }}</span>
                                        <span>{{ $enrollment->group->schedule_day }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-text-secondary italic">غير مسجل في أي حلقات حالياً.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-3">آخر تسجيل حضور</h4>
                        @php
                            $lastAttendance = $child->attendance->sortByDesc('date')->first();
                        @endphp
                        @if($lastAttendance)
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-border bg-bg/20">
                                <div class="w-2 h-2 rounded-full {{ $lastAttendance->present ? 'bg-success' : 'bg-error' }}"></div>
                                <span class="text-sm font-medium text-text-primary">
                                    {{ $lastAttendance->present ? 'حاضر' : 'غائب' }} - {{ \Carbon\Carbon::parse($lastAttendance->date)->format('Y-m-d') }}
                                </span>
                            </div>
                        @else
                            <p class="text-xs text-text-secondary">لا يوجد سجل حضور مسجل بعد.</p>
                        @endif
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
@endif
@endsection
