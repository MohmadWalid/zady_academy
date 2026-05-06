@extends('layouts.app')

@section('title', 'إضافة طالب جديد')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('secretary.academic.students.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="إضافة طالب جديد" subtitle="تسجيل طالب جديد في النظام" />
    </div>

    <x-card>
        <form action="{{ route('secretary.academic.students.store') }}" method="POST" class="flex flex-col gap-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">اسم الطالب</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="الاسم الرباعي" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">العمر</label>
                    <input type="number" name="age" required value="{{ old('age') }}" placeholder="السعر" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    @error('age') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="p-4 rounded-xl bg-bg/50 border border-border">
                <h4 class="font-bold text-text-primary mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    بيانات ولي الأمر
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">رقم هاتف ولي الأمر</label>
                        <input type="text" name="parent_phone" required value="{{ old('parent_phone') }}" placeholder="05xxxxxxxx" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                        @error('parent_phone') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">اسم ولي الأمر</label>
                        <input type="text" name="parent_name" required value="{{ old('parent_name') }}" placeholder="الاسم الكامل" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                        @error('parent_name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p class="text-xs text-text-secondary mt-3">سيقوم النظام بالبحث عن ولي الأمر برقم الهاتف، وفي حال عدم وجوده سيتم إنشاء حساب جديد له تلقائياً.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">رقم هاتف الطالب (اختياري)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="05xxxxxxxx" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">العنوان (اختياري)</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="الحي، الشارع..." class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white h-12 rounded-xl font-bold hover:bg-primary-dark transition-all">حفظ الطالب</button>
                <a href="{{ route('secretary.academic.students.index') }}" class="flex-1 bg-surface border border-border text-text-secondary h-12 rounded-xl font-bold flex items-center justify-center hover:bg-bg transition-all">إلغاء</a>
            </div>
        </form>
    </x-card>
</div>
@endsection
