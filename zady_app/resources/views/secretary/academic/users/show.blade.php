@extends('layouts.app')

@section('title', 'بيانات ولي الأمر')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('secretary.academic.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="بيانات ولي الأمر" subtitle="متابعة الأبناء وكود الدخول" />
    </div>

    <x-card class="mb-8">
        <div class="flex justify-between items-start mb-8 pb-6 border-b border-border">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary-light text-primary rounded-2xl flex items-center justify-center font-bold text-2xl">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-text-primary">{{ $user->name }}</h3>
                    <p class="text-sm text-text-secondary">ولي أمر</p>
                </div>
            </div>
            <div class="text-left">
                <span class="block text-xs text-text-secondary mb-1">كود الدخول</span>
                <span class="text-2xl font-black text-primary tracking-widest">{{ $user->access_code }}</span>
            </div>
        </div>

        <form action="{{ route('secretary.academic.users.update', $user) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">الاسم</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full h-12 px-4 border border-border rounded-xl bg-bg outline-none focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full h-12 px-4 border border-border rounded-xl bg-bg outline-none focus:ring-primary">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full h-12 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors">تحديث البيانات</button>
            </div>
        </form>
    </x-card>

    <h3 class="text-lg font-bold text-text-primary mb-4">الأبناء المسجلون</h3>
    <div class="grid grid-cols-1 gap-4">
        @forelse($user->children as $child)
            <x-card class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-bg rounded-lg flex items-center justify-center text-primary font-bold">
                        {{ mb_substr($child->name, 0, 1) }}
                    </div>
                    <div>
                        <span class="block font-bold text-text-primary">{{ $child->name }}</span>
                        <div class="flex gap-2">
                            @foreach($child->activeEnrollments as $enrollment)
                                <span class="text-[10px] bg-bg px-2 py-0.5 rounded border border-border">{{ $enrollment->group->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <a href="{{ route('secretary.academic.students.show', $child) }}" class="text-sm text-primary hover:underline">الملف الشخصي ←</a>
            </x-card>
        @empty
            <p class="text-center py-8 text-text-secondary bg-bg rounded-2xl border border-border italic">لا يوجد أبناء مسجلون حالياً.</p>
        @endforelse
    </div>
</div>
@endsection
