@extends('layouts.app')

@section('title', 'سجل المدفوعات')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="سجل المدفوعات" subtitle="تتبع حالة مدفوعاتك السابقة" />
    
    <div class="flex gap-2">
        <a href="{{ route('parent.payments.upload') }}" class="h-[48px] px-6 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors flex items-center justify-center whitespace-nowrap shadow-sm">
            رفع إيصال جديد
        </a>
    </div>
</div>

@if($payments->isEmpty())
    <x-card>
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
            title="لا يوجد مدفوعات مسجلة" 
            subtitle="لم تقم برفع أي إيصالات تحويل بعد." 
            actionLabel="رفع أول إيصال" 
            actionRoute="{{ route('parent.payments.upload') }}" />
    </x-card>
@else
    <x-card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="text-xs text-text-secondary bg-bg uppercase border-b border-border">
                    <tr>
                        <th class="px-6 py-4 rounded-tr-xl">الكود</th>
                        <th class="px-6 py-4">الطالب / الحلقة</th>
                        <th class="px-6 py-4">المبلغ</th>
                        <th class="px-6 py-4">الحالة</th>
                        <th class="px-6 py-4 rounded-tl-xl text-left">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($payments as $payment)
                        <tr class="hover:bg-bg transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-text-primary">{{ $payment->payment_code }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-text-primary">{{ $payment->subscription->student->name }}</div>
                                <div class="text-xs text-text-secondary">{{ $payment->subscription->group->name }} ({{ $payment->subscription->month }})</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-primary">{{ number_format($payment->amount) }} جنيه</td>
                            <td class="px-6 py-4"><x-status-badge :status="$payment->status" /></td>
                            <td class="px-6 py-4 text-left text-text-secondary text-xs">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-6">
        {{ $payments->links() }}
    </div>
@endif
@endsection
