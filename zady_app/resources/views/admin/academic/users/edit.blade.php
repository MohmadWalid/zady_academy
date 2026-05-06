@extends('layouts.app')

@section('title', 'تعديل مستخدم')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.academic.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="تعديل مستخدم" subtitle="تحديث بيانات {{ $user->name }}" />
    </div>

    <x-card>
        <form action="{{ route('admin.academic.users.update', $user) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">الدور الوظيفي</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach(['teacher' => 'معلم', 'secretary' => 'سكرتير', 'admin' => 'مدير', 'parent' => 'ولي أمر'] as $val => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="{{ $val }}" class="peer hidden" {{ $user->role === $val ? 'checked' : '' }}>
                            <div class="p-4 border border-border rounded-xl text-center peer-checked:border-primary peer-checked:bg-primary-light/10 peer-checked:text-primary transition-all">
                                <span class="block font-bold text-sm">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="p-4 bg-surface border border-border rounded-xl">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-text-secondary">كود الدخول (ثابت)</span>
                    <span class="font-bold text-primary tracking-widest">{{ $user->access_code }}</span>
                </div>
            </div>

            <button type="submit" class="w-full h-[54px] bg-primary text-white font-bold rounded-2xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 mt-4">
                تحديث البيانات
            </button>
        </form>
    </x-card>
</div>
@endsection
