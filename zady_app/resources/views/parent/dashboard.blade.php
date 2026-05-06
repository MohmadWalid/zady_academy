@extends('layouts.app')

@section('title', 'لوحة ولي الأمر')

@section('content')
<x-page-header title="مرحباً بك، {{ auth()->user()->name }}" subtitle="متابعة حالة أبنائك والمدفوعات" />

@if($unpaidAmount > 0)
    <!-- Alert Strip -->
    <div class="mb-8 bg-warning-bg border border-warning/20 p-5 rounded-2xl flex flex-col sm:flex-row items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center text-warning flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="flex-1 text-center sm:text-right">
            <h4 class="font-bold text-text-primary">يوجد اشتراكات مستحقة الدفع</h4>
            <p class="text-sm text-text-secondary mt-0.5">إجمالي المبلغ المطلوب: <span class="font-bold text-warning-800">{{ number_format($unpaidAmount) }} جنيه</span></p>
        </div>
        <a href="{{ route('parent.payments.upload') }}" class="w-full sm:w-auto px-8 py-3 bg-warning text-white rounded-xl font-bold hover:bg-yellow-600 transition-all shadow-lg shadow-warning/20 whitespace-nowrap text-center">
            ادفع الآن
        </a>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    @forelse($children as $child)
        <x-card class="relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-2 h-full bg-primary-light group-hover:bg-primary transition-colors"></div>
            
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-primary-light text-primary rounded-2xl flex items-center justify-center font-bold text-2xl shadow-inner">
                        {{ mb_substr($child->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-text-primary">{{ $child->name }}</h3>
                        <p class="text-sm text-text-secondary">العمر: {{ $child->age }} سنوات</p>
                    </div>
                </div>
                <x-status-badge status="active" />
            </div>
            
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-text-secondary uppercase tracking-wider">الحلقات المسجل بها</h4>
                <div class="flex flex-col gap-3">
                    @forelse($child->activeEnrollments as $enrollment)
                        <div class="bg-bg/50 p-4 rounded-xl border border-border flex justify-between items-center group/item hover:border-primary/30 transition-colors">
                            <div>
                                <span class="block font-bold text-text-primary">{{ $enrollment->group->name }}</span>
                                <span class="text-xs text-text-secondary">المعلم: {{ $enrollment->group->teacher->name ?? 'غير محدد' }}</span>
                            </div>
                            <div class="text-left">
                                <span class="block text-xs font-bold text-primary">{{ number_format($enrollment->group->monthly_price) }} جنيه</span>
                                <span class="text-[10px] text-text-secondary">شهرياً</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-text-secondary italic">غير مسجل في أي حلقات حالياً.</p>
                    @endforelse
                </div>
            </div>
        </x-card>
    @empty
        <div class="md:col-span-2">
            <x-card>
                <x-empty-state 
                    icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
                    title="لا يوجد أبناء مسجلين" 
                    subtitle="لم يتم ربط أي طلاب بحسابك بعد. يرجى مراجعة إدارة الأكاديمية." />
            </x-card>
        </div>
    @endforelse
</div>

<x-card>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-text-primary">آخر الاشتراكات وحالتها</h3>
        <a href="{{ route('parent.payments.index') }}" class="text-sm font-bold text-primary hover:underline">عرض كل المدفوعات ←</a>
    </div>
    
    @if($recentSubscriptions->isEmpty())
        <p class="text-center text-text-secondary py-4">لا توجد اشتراكات مسجلة حالياً.</p>
    @else
        <div class="space-y-3">
            @foreach($recentSubscriptions as $sub)
                <div class="flex items-center justify-between p-4 rounded-xl border border-border bg-bg/20 hover:bg-bg/40 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="text-center min-w-[60px]">
                            <span class="block text-xs text-text-secondary">شهر</span>
                            <span class="block font-bold text-text-primary">{{ $sub->month }}</span>
                        </div>
                        <div class="h-8 w-[1px] bg-border"></div>
                        <div>
                            <span class="block font-bold text-sm text-text-primary">{{ $sub->student->name }}</span>
                            <span class="block text-xs text-text-secondary">{{ $sub->group->name }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-sm text-text-primary">{{ number_format($sub->group->monthly_price) }} جنيه</span>
                        <x-status-badge :status="$sub->status" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-card>
@endsection
