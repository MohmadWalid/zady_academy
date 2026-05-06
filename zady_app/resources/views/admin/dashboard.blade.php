@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <x-page-header title="مرحباً، {{ auth()->user()->name }}" subtitle="إليك نظرة عامة على نشاط الأكاديمية اليوم" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <x-stat-card label="إجمالي الإيرادات" value="{{ number_format($totalRevenue) }} جنيه" />
        <x-stat-card label="التحصيل النقدي (اليوم)" value="{{ number_format($cashToday) }} جنيه" />
        <x-stat-card label="الطلاب النشطين" value="{{ $activeStudents }}" />
        <x-stat-card label="الحلقات النشطة" value="{{ $activeGroups }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card class="h-64 flex items-center justify-center">
            <p class="text-text-secondary">رسم بياني للإيرادات (قريباً)</p>
        </x-card>
        <x-card class="h-64 flex items-center justify-center">
            <p class="text-text-secondary">نشاط التحضير (قريباً)</p>
        </x-card>
    </div>

    <div class="mt-8">
        <h3 class="text-lg font-bold text-text-primary mb-4">إجراءات إدارية</h3>
        <x-card class="bg-primary/5 border-primary/10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h4 class="font-bold text-primary">توليد اشتراكات الشهر الحالي</h4>
                    <p class="text-sm text-text-secondary mt-1">سيقوم النظام بإنشاء مطالبات دفع لجميع الطلاب المسجلين في حلقات نشطة لشهر {{ now()->format('Y-m') }}.</p>
                </div>
                <form action="{{ route('admin.academic.subscriptions.generate') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في توليد الاشتراكات الآن؟')">
                    @csrf
                    <input type="hidden" name="month" value="{{ now()->format('Y-m') }}">
                    <button type="submit" class="h-12 px-8 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 whitespace-nowrap">
                        توليد الآن
                    </button>
                </form>
            </div>
        </x-card>
    </div>
@endsection