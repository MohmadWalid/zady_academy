@extends('layouts.app')

@section('title', 'لوحة السكرتير')

@section('content')
<x-page-header title="مرحباً، {{ auth()->user()->name }}" subtitle="لوحة تحكم السكرتارية" />

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <x-stat-card label="التحصيل النقدي (اليوم)" value="{{ number_format($cashToday) }} جنيه" />
    <x-stat-card label="حوالات قيد المراجعة" value="{{ $pendingTransfersCount }}" />
    <x-stat-card label="استفسارات واردة" value="{{ $inquiriesCount }}" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-card>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-text-primary">تسجيل دفعة نقدية سريعة</h3>
            <a href="{{ route('secretary.payments.cash') }}" class="text-sm font-medium text-primary hover:underline">المزيد</a>
        </div>
        <form action="{{ route('secretary.payments.cash') }}" method="GET" class="flex flex-col gap-4">
            <x-search-bar name="search" placeholder="ابحث باسم الطالب أو كوده..." />
            <div class="bg-bg p-3 rounded-xl border border-border text-xs text-text-secondary">
                سيتم تحويلك لصفحة التحصيل النقدي بعد الضغط على زر المتابعة.
            </div>
            <button type="submit" class="w-full h-[48px] bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors">
                متابعة التحصيل
            </button>
        </form>
    </x-card>

    <x-card>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-text-primary">أحدث الحوالات بانتظار التأكيد</h3>
            <a href="{{ route('secretary.payments.pending') }}" class="text-sm font-medium text-primary hover:underline">عرض الكل</a>
        </div>
        <x-empty-state 
            icon='<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            title="لا يوجد حوالات معلقة" 
            subtitle="جميع الحوالات تمت مراجعتها." />
    </x-card>
</div>
@endsection
