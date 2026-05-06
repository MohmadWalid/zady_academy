@extends('layouts.app')

@section('title', 'بيانات المستخدم')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.academic.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="بيانات المستخدم" subtitle="كود الدخول والمعلومات الشخصية" />
    </div>

    @if(session('reveal_code'))
        <div class="bg-success-bg border border-success/20 rounded-2xl p-8 text-center mb-8 shadow-xl shadow-success/10 animate-in fade-in zoom-in duration-500">
            <div class="w-16 h-16 bg-success text-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-success-800 mb-2">تم إنشاء الحساب بنجاح!</h2>
            <p class="text-success-700 mb-8">هذا هو كود الدخول الخاص بـ {{ $user->name }}. يرجى إبلاغه به يدوياً.</p>
            
            <div class="relative group max-w-xs mx-auto">
                <div class="bg-surface border-2 border-dashed border-success p-6 rounded-2xl text-4xl font-black text-success tracking-widest cursor-pointer hover:bg-success-bg transition-colors" onclick="copyCode('{{ $user->access_code }}')">
                    {{ $user->access_code }}
                </div>
                <p class="mt-4 text-xs text-success-600 font-medium">اضغط على الكود للنسخ</p>
            </div>
        </div>
    @endif

    <x-card>
        <div class="flex items-center gap-6 mb-8 pb-8 border-b border-border">
            <div class="w-20 h-20 bg-primary-light text-primary rounded-3xl flex items-center justify-center font-bold text-4xl">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-2xl font-bold text-text-primary">{{ $user->name }}</h3>
                <p class="text-text-secondary">{{ $user->role }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h4 class="text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">رقم الهاتف</h4>
                <p class="text-lg font-bold text-text-primary">{{ $user->phone }}</p>
            </div>
            <div>
                <h4 class="text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">كود الدخول (للدعم)</h4>
                <p class="text-lg font-bold text-primary">{{ $user->access_code }}</p>
            </div>
            <div>
                <h4 class="text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">تاريخ الانضمام</h4>
                <p class="text-lg font-bold text-text-primary">{{ $user->created_at->format('Y-m-d') }}</p>
            </div>
            <div>
                <h4 class="text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">حالة الحساب</h4>
                <x-status-badge status="active" />
            </div>
        </div>

        @if($user->role === 'teacher')
            <div class="mt-12">
                <h4 class="text-xs font-bold text-text-secondary uppercase tracking-widest mb-4">الحلقات المكلف بها</h4>
                <div class="grid grid-cols-1 gap-4">
                    @forelse($user->assignedGroups as $group)
                        <div class="flex items-center justify-between p-4 bg-bg rounded-xl border border-border">
                            <span class="font-bold text-text-primary">{{ $group->name }}</span>
                            <a href="{{ route('admin.academic.groups.show', $group) }}" class="text-sm text-primary hover:underline font-medium">عرض الحلقة ←</a>
                        </div>
                    @empty
                        <p class="text-sm text-text-secondary italic">لا توجد حلقات مكلف بها حالياً.</p>
                    @endforelse
                </div>
            </div>
        @endif
        
        <div class="flex gap-4 mt-12 pt-8 border-t border-border">
            <a href="{{ route('admin.academic.users.edit', $user) }}" class="flex-1 h-12 bg-surface border border-border rounded-xl flex items-center justify-center font-bold text-text-primary hover:bg-bg transition-colors">
                تعديل البيانات
            </a>
        </div>
    </x-card>
</div>

<script>
    function copyCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('تم نسخ الكود: ' + code);
        });
    }
</script>
@endsection
