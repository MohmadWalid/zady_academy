@extends('layouts.app')

@section('title', 'التحويلات الناجحة')

@section('content')
<div class="mb-8">
    <x-page-header title="التحويلات الناجحة" subtitle="سجل التحويلات البنكية والمحافظ التي تم قبولها" />
</div>

<x-card class="mb-8">
    <form action="{{ route('admin.payments.transfers') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب أو كود العملية..." />
        </div>
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-xl font-bold hover:bg-primary-dark transition-colors h-[48px]">
            بحث
        </button>
    </form>
</x-card>

@if($payments->isEmpty())
    <x-card>
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            title="لا توجد تحويلات مقبولة" 
            subtitle="لم يتم العثور على أي تحويلات بنكية مقبولة في هذا القسم." />
    </x-card>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-data="{ openDetail: null }">
        @foreach($payments as $payment)
            <div class="relative group">
                <x-card class="h-full border-2 border-transparent hover:border-primary/20 transition-all cursor-pointer" @click="openDetail = {{ $payment->id }}">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 rounded-full bg-success-bg text-success text-[10px] font-bold uppercase tracking-wider">مقبول</span>
                        <span class="text-xs text-text-secondary">{{ $payment->created_at->format('Y/m/d') }}</span>
                    </div>

                    <h4 class="text-lg font-bold text-text-primary mb-1">{{ $payment->subscription->student->name }}</h4>
                    <div class="text-sm text-text-secondary mb-4">{{ $payment->subscription->group->name }} - {{ $payment->subscription->month }}</div>

                    <div class="flex justify-between items-center pt-4 border-t border-border">
                        <div class="text-xl font-bold text-primary">{{ number_format($payment->amount, 2) }} ج.م</div>
                        <button class="text-sm font-bold text-text-secondary group-hover:text-primary transition-colors">عرض التفاصيل ←</button>
                    </div>
                </x-card>

                <!-- Modal / Bottom Sheet Overlay -->
                <template x-if="openDetail === {{ $payment->id }}">
                    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60" @click.self="openDetail = null">
                        <div class="bg-white w-full max-w-lg rounded-t-3xl sm:rounded-3xl overflow-hidden shadow-2xl animate-slide-up" @click.stop>
                            <div class="p-6 border-b border-border flex justify-between items-center bg-bg/30">
                                <h3 class="text-xl font-bold text-text-primary">تفاصيل عملية الدفع</h3>
                                <button @click="openDetail = null" class="p-2 text-text-secondary hover:text-text-primary transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="p-6">
                                <div class="flex flex-col items-center text-center mb-8">
                                    <div class="w-16 h-16 rounded-full bg-success/10 text-success flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div class="text-3xl font-bold text-text-primary mb-1">{{ number_format($payment->amount, 2) }} ج.م</div>
                                    <div class="text-sm text-text-secondary">كود العملية: {{ $payment->payment_code }}</div>
                                </div>

                                <div class="space-y-4 mb-8">
                                    <div class="flex justify-between py-3 border-b border-border/50">
                                        <span class="text-text-secondary">الطالب</span>
                                        <span class="font-bold text-text-primary">{{ $payment->subscription->student->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-border/50">
                                        <span class="text-text-secondary">الحلقة</span>
                                        <span class="font-bold text-text-primary">{{ $payment->subscription->group->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-border/50">
                                        <span class="text-text-secondary">الشهر</span>
                                        <span class="font-bold text-text-primary">{{ $payment->subscription->month }}</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-border/50">
                                        <span class="text-text-secondary">تمت المراجعة بواسطة</span>
                                        <span class="font-bold text-primary">{{ $payment->updater->name ?? '---' }}</span>
                                    </div>
                                </div>

                                @if($payment->proof_image)
                                    <div class="mb-6">
                                        <label class="block text-sm font-bold text-text-secondary mb-3">إيصال التحويل</label>
                                        <div class="rounded-2xl overflow-hidden border border-border">
                                            <img src="{{ route('proofs.show', $payment->payment_code) }}" alt="إيصال الدفع" class="w-full h-auto">
                                        </div>
                                    </div>
                                @endif

                                <div class="flex gap-4">
                                    @if($payment->status === 'approved' && auth()->user()->role === 'admin')
                                        <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" class="flex-1" onsubmit="return confirm('هل أنت متأكد من استرداد هذه الدفعة؟ سيؤدي ذلك لإرجاع حالة الاشتراك لغير مدفوع.')">
                                            @csrf
                                            <button type="submit" class="w-full h-12 bg-error/10 text-error font-bold rounded-xl hover:bg-error hover:text-white transition-all">استرداد (Refund)</button>
                                        </form>
                                    @endif
                                    <button @click="openDetail = null" class="flex-1 h-12 bg-bg text-text-primary font-bold rounded-xl hover:bg-border transition-all">إغلاق</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $payments->links() }}
    </div>
@endif

<style>
    @keyframes slide-up {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }
    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }
</style>
@endsection
