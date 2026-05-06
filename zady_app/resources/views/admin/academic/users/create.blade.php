@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.academic.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="إضافة كادر جديد" subtitle="إنشاء حساب لمدير، سكرتير، أو معلم" />
    </div>

    <x-card>
        <form action="{{ route('admin.academic.users.store') }}" method="POST" class="flex flex-col gap-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none" placeholder="مثال: أحمد محمد" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none" placeholder="01xxxxxxxxx" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">الدور الوظيفي</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="teacher" class="peer hidden" checked>
                        <div class="p-4 border border-border rounded-xl text-center peer-checked:border-primary peer-checked:bg-primary-light/10 peer-checked:text-primary transition-all">
                            <span class="block font-bold">معلم</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="secretary" class="peer hidden">
                        <div class="p-4 border border-border rounded-xl text-center peer-checked:border-primary peer-checked:bg-primary-light/10 peer-checked:text-primary transition-all">
                            <span class="block font-bold">سكرتير</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="admin" class="peer hidden">
                        <div class="p-4 border border-border rounded-xl text-center peer-checked:border-primary peer-checked:bg-primary-light/10 peer-checked:text-primary transition-all">
                            <span class="block font-bold">مدير</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-xl flex items-start gap-3 border border-blue-100">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-xs text-blue-800 leading-relaxed">
                    سيتم توليد كود دخول (Access Code) تلقائياً لهذا المستخدم بمجرد الحفظ. تأكد من نسخ الكود وإعطائه للموظف حيث لا يمكنه الدخول بدونه.
                </p>
            </div>

            <button type="submit" class="w-full h-[54px] bg-primary text-white font-bold rounded-2xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 mt-4">
                حفظ المستخدم وتوليد الكود
            </button>
        </form>
    </x-card>
</div>
@endsection
