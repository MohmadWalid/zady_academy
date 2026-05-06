@extends('layouts.app')

@section('title', 'تعديل بيانات الطالب')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.academic.students.show', $student) }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="تعديل بيانات الطالب" subtitle="{{ $student->name }}" />
    </div>

    <x-card>
        <form action="{{ route('admin.academic.students.update', $student) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">اسم الطالب</label>
                    <input type="text" name="name" required value="{{ old('name', $student->name) }}" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">العمر</label>
                    <input type="number" name="age" required value="{{ old('age', $student->age) }}" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    @error('age') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">رقم هاتف الطالب (اختياري)</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">العنوان (اختياري)</label>
                    <input type="text" name="address" value="{{ old('address', $student->address) }}" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                </div>
            </div>

            <div class="p-4 rounded-xl bg-bg/50 border border-border">
                <p class="text-sm text-text-secondary">ملاحظة: لتغيير ولي الأمر، يرجى التواصل مع الدعم الفني أو تعديل قاعدة البيانات مباشرة لضمان عدم تأثر سجلات المدفوعات.</p>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white h-12 rounded-xl font-bold hover:bg-primary-dark transition-all">تحديث البيانات</button>
                <a href="{{ route('admin.academic.students.show', $student) }}" class="flex-1 bg-surface border border-border text-text-secondary h-12 rounded-xl font-bold flex items-center justify-center hover:bg-bg transition-all">إلغاء</a>
            </div>
        </form>
    </x-card>
</div>
@endsection
