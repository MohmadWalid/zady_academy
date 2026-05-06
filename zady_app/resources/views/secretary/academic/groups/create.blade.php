@extends('layouts.app')

@section('title', 'إنشاء حلقة جديدة')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('secretary.academic.groups.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="إنشاء حلقة جديدة" />
    </div>

    <x-card>
        <form action="{{ route('secretary.academic.groups.store') }}" method="POST" class="flex flex-col gap-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">اسم الحلقة</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="مثال: حلقة الإمام البخاري" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">نوع الحلقة</label>
                    <select name="type" required class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                        <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>عامة (تحفيظ)</option>
                        <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>خاصة (فردي)</option>
                        <option value="course" {{ old('type') == 'course' ? 'selected' : '' }}>دورة مكثفة</option>
                    </select>
                    @error('type') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">السعر الشهري (جنيه)</label>
                    <input type="number" name="monthly_price" required value="{{ old('monthly_price') }}" placeholder="0" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    @error('monthly_price') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">المعلم المسؤول</label>
                <select name="teacher_id" required class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    <option value="">اختر المعلم...</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_id') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white h-12 rounded-xl font-bold hover:bg-primary-dark transition-all">حفظ الحلقة</button>
                <a href="{{ route('secretary.academic.groups.index') }}" class="flex-1 bg-surface border border-border text-text-secondary h-12 rounded-xl font-bold flex items-center justify-center hover:bg-bg transition-all">إلغاء</a>
            </div>
        </form>
    </x-card>
    
    <div class="mt-6 p-4 rounded-xl bg-primary-light/50 border border-primary/10 flex items-center gap-3">
        <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="text-sm text-primary-dark font-medium">بعد إنشاء الحلقة، ستتمكن من إضافة المواعيد الأسبوعية (بحد أقصى موعدين) من صفحة تفاصيل الحلقة.</p>
    </div>
</div>
@endsection
