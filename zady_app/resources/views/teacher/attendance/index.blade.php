@extends('layouts.app')

@section('title', 'التحضير')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="سجل التحضير" subtitle="اختر حلقة لتحضير الطلاب ليوم {{ $date }}" />
    
    <div class="flex gap-2">
        <form action="{{ route('teacher.attendance.index') }}" method="GET" id="date-picker-form">
            <input type="date" name="date" value="{{ $date }}" onchange="document.getElementById('date-picker-form').submit()" class="h-[48px] px-4 border border-border rounded-[24px] bg-surface text-sm focus:ring-primary focus:border-primary outline-none">
        </form>
    </div>
</div>

@if($groups->isEmpty())
    <x-card>
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
            title="لا توجد حلقات" 
            subtitle="لا توجد حلقات مضافة في النظام حالياً." />
    </x-card>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($groups as $group)
            <a href="{{ route('teacher.attendance.show', ['group' => $group, 'date' => $date]) }}" class="block hover:-translate-y-1 transition-transform">
                <x-group-card :group="$group" :studentCount="$group->active_enrollments_count" />
            </a>
        @endforeach
    </div>
@endif
@endsection
