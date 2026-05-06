@extends('layouts.app')

@section('title', 'تعديل الحلقة')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('secretary.academic.groups.show', $group) }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="تعديل بيانات الحلقة" subtitle="{{ $group->name }}" />
    </div>

    <x-card>
        <form action="{{ route('secretary.academic.groups.update', $group) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">اسم الحلقة</label>
                <input type="text" name="name" required value="{{ old('name', $group->name) }}" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">نوع الحلقة</label>
                    <select name="type" required class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                        <option value="general" {{ old('type', $group->type) == 'general' ? 'selected' : '' }}>عامة (تحفيظ)</option>
                        <option value="private" {{ old('type', $group->type) == 'private' ? 'selected' : '' }}>خاصة (فردي)</option>
                        <option value="course" {{ old('type', $group->type) == 'course' ? 'selected' : '' }}>دورة مكثفة</option>
                    </select>
                    @error('type') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">السعر الشهري (جنيه)</label>
                    <input type="number" name="monthly_price" required value="{{ old('monthly_price', $group->monthly_price) }}" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    @error('monthly_price') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">المعلم المسؤول</label>
                <select name="teacher_id" required class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    <option value="">اختر المعلم...</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $group->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_id') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white h-12 rounded-xl font-bold hover:bg-primary-dark transition-all">تحديث البيانات</button>
                <a href="{{ route('secretary.academic.groups.show', $group) }}" class="flex-1 bg-surface border border-border text-text-secondary h-12 rounded-xl font-bold flex items-center justify-center hover:bg-bg transition-all">إلغاء</a>
            </div>
        </form>
    </x-card>
</div>
@endsection
