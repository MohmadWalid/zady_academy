@extends('layouts.app')

@section('title', 'سجل المدفوعات')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="سجل المدفوعات" subtitle="جميع العمليات المسجلة في النظام" />
    <form action="{{ route('secretary.payments.history') }}" method="GET" class="w-full sm:w-80">
        <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب أو كود العملية..." />
    </form>
</div>

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="text-xs text-text-secondary bg-bg uppercase border-b border-border">
                <tr>
                    <th class="px-6 py-4 rounded-tr-xl">الكود</th>
                    <th class="px-6 py-4">الطالب</th>
                    <th class="px-6 py-4">الحلقة</th>
                    <th class="px-6 py-4">المبلغ</th>
                    <th class="px-6 py-4">الطريقة</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4 rounded-tl-xl text-left">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($payments as $payment)
                    <tr class="hover:bg-bg transition-colors">
                        <td class="px-6 py-4 font-medium text-text-primary">{{ $payment->payment_code }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-text-primary">{{ $payment->subscription->student->name }}</div>
                            <div class="text-xs text-text-secondary">كود: {{ $payment->subscription->student_id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-text-primary">{{ $payment->subscription->group->name }}</div>
                            <div class="text-xs text-text-secondary">شهر: {{ $payment->subscription->month }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-primary">{{ number_format($payment->amount) }} جنيه</td>
                        <td class="px-6 py-4 text-xs">
                            @if($payment->method === 'cash')
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-md">نقدي</span>
                            @else
                                <span class="px-2 py-1 bg-purple-50 text-purple-600 rounded-md">حوالة</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$payment->status" />
                        </td>
                        <td class="px-6 py-4 text-left text-text-secondary text-xs">
                            {{ $payment->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-text-secondary">لا يوجد عمليات دفع مسجلة بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $payments->links() }}
    </div>
</x-card>
@endsection
