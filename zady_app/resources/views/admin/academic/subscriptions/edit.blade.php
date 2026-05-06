@extends('layouts.app')

@section('title', 'تعديل اشتراك')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('admin.academic.subscriptions.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </a>
    <x-page-header title="تعديل اشتراك" subtitle="تصحيح بيانات اشتراك الطالب: {{ $subscription->student->name }}" />
</div>

<div class="max-w-2xl">
    <x-card>
        <form action="{{ route('admin.academic.subscriptions.update', $subscription) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-text-primary mb-2">الطالب (للقراءة فقط)</label>
                <div class="h-12 px-4 rounded-xl border border-border bg-bg/50 flex items-center text-text-secondary">
                    {{ $subscription->student->name }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-text-primary mb-2">الشهر (YYYY-MM)</label>
                <input type="text" name="month" value="{{ $subscription->month }}" placeholder="2024-05" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                <p class="mt-1 text-xs text-text-secondary">أدخل الشهر بالتنسيق: السنة-الشهر (مثال: 2024-05)</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-text-primary mb-2">الحلقة</label>
                <select name="group_id" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ $subscription->group_id == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-text-primary mb-2">حالة الاشتراك</label>
                <select name="status" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    <option value="unpaid" {{ $subscription->status == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                    <option value="pending" {{ $subscription->status == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="paid" {{ $subscription->status == 'paid' ? 'selected' : '' }}>مدفوع</option>
                </select>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-xl font-bold hover:bg-primary-dark transition-all shadow-lg">حفظ التغييرات</button>
                <a href="{{ route('admin.academic.subscriptions.index') }}" class="flex-1 bg-bg text-text-primary py-3 rounded-xl font-bold hover:bg-border transition-all text-center">إلغاء</a>
            </div>
        </form>
    </x-card>
</div>
@endsection
