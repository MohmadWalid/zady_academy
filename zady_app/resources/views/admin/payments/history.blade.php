@extends('layouts.app')

@section('title', 'سجل المدفوعات')

@section('content')
<div class="mb-8">
    <x-page-header title="سجل المدفوعات" subtitle="تتبع جميع العمليات المالية (كاش وتحويلات)" />
</div>

<x-card class="mb-8">
    <form action="{{ route('admin.payments.history') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب أو كود العملية..." />
        </div>
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-xl font-bold hover:bg-primary-dark transition-colors h-[48px]">
            بحث
        </button>
    </form>
</x-card>

<x-card class="p-0 overflow-hidden" x-data="{ openDetail: null, activePayment: null }">
    @if($payments->isEmpty())
        <div class="p-12">
            <x-empty-state 
                icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
                title="لا توجد عمليات دفع" 
                subtitle="لم يتم العثور على أي عمليات دفع تطابق بحثك." />
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-bg text-text-secondary text-sm">
                        <th class="px-6 py-4 font-bold border-b border-border">الطالب</th>
                        <th class="px-6 py-4 font-bold border-b border-border">كود العملية</th>
                        <th class="px-6 py-4 font-bold border-b border-border">النوع</th>
                        <th class="px-6 py-4 font-bold border-b border-border">المبلغ</th>
                        <th class="px-6 py-4 font-bold border-b border-border">الحالة</th>
                        <th class="px-6 py-4 font-bold border-b border-border">التاريخ</th>
                        <th class="px-6 py-4 font-bold border-b border-border text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($payments as $payment)
                        <tr class="hover:bg-bg transition-colors cursor-pointer" @click="openDetail = {{ $payment->id }}">
                            <td class="px-6 py-4">
                                <div class="font-bold text-text-primary">{{ $payment->subscription->student->name }}</div>
                                <div class="text-xs text-text-secondary">{{ $payment->subscription->group->name }} - {{ $payment->subscription->month }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-text-secondary">{{ $payment->payment_code }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $payment->method === 'cash' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                    {{ $payment->method === 'cash' ? 'نقدي' : 'تحويل' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-text-primary">{{ number_format($payment->amount, 2) }} ج.م</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusLabels = ['pending' => 'قيد المراجعة', 'approved' => 'مقبول', 'rejected' => 'مرفوض', 'refunded' => 'مسترد'];
                                    $statusColors = ['pending' => 'bg-warning-bg text-warning', 'approved' => 'bg-success-bg text-success', 'rejected' => 'bg-error/10 text-error', 'refunded' => 'bg-bg text-text-secondary'];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$payment->status] }}">
                                    {{ $statusLabels[$payment->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-text-secondary">{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                            <td class="px-6 py-4 text-left">
                                <button class="text-primary hover:underline text-xs font-bold">عرض ←</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $payments->links() }}
            </div>
        @endif
    @endif

    <!-- Dynamic Detail Modal (shared for all rows) -->
    @foreach($payments as $payment)
    <template x-if="openDetail === {{ $payment->id }}">
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60" @click.self="openDetail = null">
            <div class="bg-white w-full max-w-lg rounded-t-3xl sm:rounded-3xl overflow-hidden shadow-2xl animate-slide-up" @click.stop>
                <div class="p-6 border-b border-border flex justify-between items-center bg-bg/30">
                    <h3 class="text-xl font-bold text-text-primary">تفاصيل عملية الدفع</h3>
                    <button @click="openDetail = null" class="p-2 text-text-secondary hover:text-text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[80vh]">
                    <div class="flex flex-col items-center text-center mb-8">
                        <div class="w-16 h-16 rounded-full {{ $payment->status === 'approved' ? 'bg-success/10 text-success' : ($payment->status === 'pending' ? 'bg-warning/10 text-warning' : 'bg-error/10 text-error') }} flex items-center justify-center mb-4">
                            @if($payment->status === 'approved') <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @elseif($payment->status === 'pending') <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @else <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> @endif
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
                            <span class="text-text-secondary">نوع الدفع</span>
                            <span class="font-bold text-text-primary">{{ $payment->method === 'cash' ? 'نقدي' : 'تحويل' }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-border/50">
                            <span class="text-text-secondary">المسؤول عن الإدخال</span>
                            <span class="font-bold text-text-primary">{{ $payment->creator->name ?? '---' }}</span>
                        </div>
                        @if($payment->status !== 'pending' && $payment->method === 'transfer')
                        <div class="flex justify-between py-3 border-b border-border/50">
                            <span class="text-text-secondary">تمت المراجعة بواسطة</span>
                            <span class="font-bold text-primary">{{ $payment->reviewer->name ?? '---' }}</span>
                        </div>
                        @endif
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
                            <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" class="flex-1" onsubmit="return confirm('هل أنت متأكد من استرداد هذه الدفعة؟')">
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
    @endforeach
</x-card>

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
