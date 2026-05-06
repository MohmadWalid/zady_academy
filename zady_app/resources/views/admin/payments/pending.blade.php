@extends('layouts.app')

@section('title', 'المدفوعات المعلقة')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="مراجعة الحوالات" subtitle="الحوالات البنكية بانتظار التأكيد" />
    <form action="{{ route('admin.payments.pending') }}" method="GET" class="w-full sm:w-80">
        <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب أو كود العملية..." />
    </form>
</div>

@if($payments->isEmpty())
    <x-card>
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
            title="لا توجد دفعات معلقة" 
            subtitle="تمت مراجعة جميع الحوالات البنكية بنجاح." />
    </x-card>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($payments as $payment)
            <x-card class="flex flex-col gap-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-full h-1 bg-warning"></div>
                
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-lg text-text-primary">{{ $payment->payment_code }}</h3>
                        <p class="text-text-secondary text-sm">{{ $payment->subscription->student->name }}</p>
                        <p class="text-xs text-text-secondary mt-1">{{ $payment->subscription->group->name }} - شهر {{ $payment->subscription->month }}</p>
                    </div>
                    <div class="text-left">
                        <span class="font-bold text-xl text-primary">{{ number_format($payment->amount) }} جنيه</span>
                    </div>
                </div>
                
                @if($payment->proof_image)
                    <div class="bg-bg rounded-lg p-2 text-center border border-border">
                        <a href="{{ route('proof.show', $payment->payment_code) }}" target="_blank" class="text-primary text-sm font-medium flex items-center justify-center gap-2 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            عرض صورة الإيصال
                        </a>
                    </div>
                @endif
                
                <div class="flex gap-2 mt-2">
                    <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="flex-1" onsubmit="return confirm('هل أنت متأكد من قبول هذه الدفعة؟')">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-success text-white rounded-lg font-medium text-sm hover:bg-green-700 transition-colors">
                            قبول
                        </button>
                    </form>
                    
                    <button type="button" onclick="openRejectModal('{{ $payment->id }}', '{{ $payment->payment_code }}')" class="flex-1 py-2 bg-danger-bg text-danger border border-danger-subtle rounded-lg font-medium text-sm hover:bg-danger hover:text-white transition-colors">
                        رفض
                    </button>
                </div>
            </x-card>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $payments->links() }}
    </div>
@endif

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-surface rounded-2xl w-full max-w-md shadow-2xl p-6">
        <h3 class="text-xl font-bold text-text-primary mb-2">رفض الدفعة</h3>
        <p class="text-sm text-text-secondary mb-6" id="reject-payment-code"></p>
        
        <form id="reject-form" method="POST" class="flex flex-col gap-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">سبب الرفض (اختياري)</label>
                <textarea name="reason" rows="3" class="w-full p-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none text-sm" placeholder="مثال: الصورة غير واضحة، المبلغ غير مطابق..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-danger text-white h-12 rounded-xl font-bold hover:bg-red-700 transition-colors">تأكيد الرفض</button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-bg text-text-secondary h-12 rounded-xl font-medium border border-border">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id, code) {
        const modal = document.getElementById('reject-modal');
        const form = document.getElementById('reject-form');
        const codeDisplay = document.getElementById('reject-payment-code');
        
        form.action = `/admin/payments/${id}/reject`;
        codeDisplay.innerText = `رقم العملية: ${code}`;
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
    }
</script>
@endsection
