@extends('layouts.app')

@section('title', 'إدارة الاشتراكات')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="إدارة الاشتراكات" subtitle="تتبع مطالبات الدفع الشهرية وحالة التحصيل" />
    
    <div class="flex gap-2">
        <form action="{{ route('admin.academic.subscriptions.generate') }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
            @csrf
            <input type="hidden" name="month" value="{{ now()->format('Y-m') }}">
            <button type="submit" class="h-[48px] px-6 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors flex items-center justify-center whitespace-nowrap shadow-sm">
                توليد اشتراكات الشهر الحالي
            </button>
        </form>
    </div>
</div>

<x-card class="mb-8">
    <form action="{{ route('admin.academic.subscriptions.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب..." />
        </div>
        <div class="w-full md:w-48">
            <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" onchange="this.form.submit()" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none">
        </div>
        <div class="w-full md:w-48">
            <select name="status" onchange="this.form.submit()" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none">
                <option value="">جميع الحالات</option>
                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مدفوع</option>
            </select>
        </div>
    </form>
</x-card>

<x-card class="p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="text-xs text-text-secondary bg-bg uppercase border-b border-border">
                <tr>
                    <th class="px-6 py-4 rounded-tr-xl">الطالب</th>
                    <th class="px-6 py-4">الحلقة</th>
                    <th class="px-6 py-4">الشهر</th>
                    <th class="px-6 py-4">المبلغ</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4 rounded-tl-xl text-left">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($subscriptions as $sub)
                    <tr class="hover:bg-bg transition-colors">
                        <td class="px-6 py-4 font-bold text-text-primary">{{ $sub->student->name }}</td>
                        <td class="px-6 py-4 text-text-secondary text-xs">{{ $sub->group->name }}</td>
                        <td class="px-6 py-4 font-medium">{{ $sub->month }}</td>
                        <td class="px-6 py-4 font-bold text-primary">{{ number_format($sub->group->monthly_price) }} جنيه</td>
                        <td class="px-6 py-4"><x-status-badge :status="$sub->status" /></td>
                        <td class="px-6 py-4 text-left">
                            <div class="flex justify-end items-center gap-2">
                                <a href="{{ route('admin.academic.subscriptions.edit', $sub) }}" class="text-primary hover:underline text-xs font-bold">تعديل</a>
                                <form action="{{ route('admin.academic.subscriptions.destroy', $sub) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger hover:underline text-xs font-bold">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-text-secondary">لا يوجد اشتراكات مطابقة للبحث.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<div class="mt-6">
    {{ $subscriptions->links() }}
</div>
@endsection
