@extends('layouts.app')

@section('title', 'تحصيل نقدي')

@section('content')
<x-page-header title="تحصيل الدفعات النقدية" subtitle="تسجيل المدفوعات المستلمة يدوياً" />

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        <x-card class="mb-6">
            <h3 class="text-lg font-bold text-text-primary mb-6">ابحث عن الطالب</h3>
            <form action="{{ route('admin.payments.cash') }}" method="GET" class="flex flex-col gap-4">
                <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب أو الكود..." />
                <button type="submit" class="h-[48px] bg-bg border border-border text-text-primary font-bold rounded-xl hover:bg-surface transition-colors">
                    بحث
                </button>
            </form>
        </x-card>

        @if($student)
            <x-card class="border-2 border-primary/20">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-primary-light text-primary rounded-xl flex items-center justify-center font-bold text-xl">
                        {{ mb_substr($student->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-text-primary text-lg">{{ $student->name }}</h4>
                        <p class="text-xs text-text-secondary">كود: ZADY-{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h5 class="text-xs font-bold text-text-secondary uppercase tracking-widest">الاشتراكات غير المدفوعة</h5>
                    <div class="flex flex-col gap-3">
                        @php $hasUnpaid = false; @endphp
                        @foreach($student->activeEnrollments as $enrollment)
                            @foreach($enrollment->subscriptions as $sub)
                                @if($sub->status === 'unpaid')
                                    @php $hasUnpaid = true; @endphp
                                    <div class="p-4 bg-bg rounded-xl border border-border flex justify-between items-center group hover:border-primary/30 transition-colors">
                                        <div>
                                            <span class="block font-bold text-text-primary">{{ $enrollment->group->name }}</span>
                                            <span class="text-xs text-text-secondary">شهر {{ $sub->month }}</span>
                                        </div>
                                        <button type="button" onclick="selectSubscription('{{ $sub->id }}', '{{ $enrollment->group->monthly_price }}', '{{ $enrollment->group->name }} ({{ $sub->month }})')" class="px-4 py-2 bg-primary-light text-primary text-xs font-bold rounded-lg hover:bg-primary hover:text-white transition-all">
                                            اختيار
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                        
                        @if(!$hasUnpaid)
                            <p class="text-sm text-text-secondary italic py-4">لا يوجد اشتراكات مستحقة حالياً لهذا الطالب.</p>
                        @endif
                    </div>
                </div>
            </x-card>
        @elseif(request('search'))
            <x-card>
                <x-empty-state 
                    icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'
                    title="لم يتم العثور على الطالب" 
                    subtitle="تأكد من كتابة الاسم أو الكود بشكل صحيح." />
            </x-card>
        @endif
    </div>

    <div id="payment-form-container" class="{{ $student ? '' : 'opacity-50 pointer-events-none' }}">
        <x-card>
            <h3 class="text-lg font-bold text-text-primary mb-6">تسجيل الدفعة</h3>
            
            <form action="{{ route('admin.payments.store_cash') }}" method="POST" class="flex flex-col gap-6" onsubmit="return confirm('هل أنت متأكد من استلام هذا المبلغ نقداً؟')">
                @csrf
                <input type="hidden" name="subscription_id" id="selected-sub-id">
                
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">الاشتراك المختار</label>
                    <input type="text" id="selected-sub-name" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg text-text-secondary cursor-not-allowed" readonly placeholder="اختر اشتراكاً من القائمة">
                </div>

                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">المبلغ المستلم (جنيه)</label>
                    <input type="number" name="amount" id="amount-input" step="0.01" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none font-bold text-lg text-primary" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">ملاحظات إضافية</label>
                    <textarea name="notes" rows="3" class="w-full p-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none resize-none" placeholder="أي ملاحظات حول العملية..."></textarea>
                </div>
                
                <button type="submit" id="submit-btn" disabled class="w-full h-[54px] bg-primary text-white font-bold rounded-2xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 disabled:bg-border disabled:shadow-none disabled:cursor-not-allowed">
                    تسجيل التحصيل النقدي
                </button>
            </form>
        </x-card>
    </div>
</div>

<script>
    function selectSubscription(id, amount, name) {
        document.getElementById('selected-sub-id').value = id;
        document.getElementById('selected-sub-name').value = name;
        document.getElementById('amount-input').value = amount;
        document.getElementById('submit-btn').disabled = false;
        
        // Visual feedback
        document.getElementById('payment-form-container').classList.remove('opacity-50', 'pointer-events-none');
    }
</script>
@endsection
