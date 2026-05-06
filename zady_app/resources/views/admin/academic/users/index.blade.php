@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="إدارة المستخدمين" subtitle="المسؤولون، السكرتارية، المعلمون، وأولياء الأمور" />
    
    <div class="flex gap-2">
        <a href="{{ route('admin.academic.users.create') }}" class="h-[48px] px-6 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors flex items-center justify-center whitespace-nowrap shadow-sm">
            إضافة كادر جديد
        </a>
    </div>
</div>

<x-card class="mb-8">
    <form action="{{ route('admin.academic.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم، الهاتف، أو الكود..." />
        </div>
        <div class="w-full md:w-48">
            <select name="role" onchange="this.form.submit()" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none">
                <option value="">جميع الأدوار</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مدير</option>
                <option value="secretary" {{ request('role') == 'secretary' ? 'selected' : '' }}>سكرتارية</option>
                <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>معلم</option>
                <option value="parent" {{ request('role') == 'parent' ? 'selected' : '' }}>ولي أمر</option>
            </select>
        </div>
    </form>
</x-card>

<x-card class="p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="text-xs text-text-secondary bg-bg uppercase border-b border-border">
                <tr>
                    <th class="px-6 py-4 rounded-tr-xl">الاسم</th>
                    <th class="px-6 py-4">الدور</th>
                    <th class="px-6 py-4">الهاتف</th>
                    <th class="px-6 py-4">كود الدخول</th>
                    <th class="px-6 py-4 rounded-tl-xl text-left">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($users as $user)
                    <tr class="hover:bg-bg transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-text-primary">{{ $user->name }}</div>
                            <div class="text-xs text-text-secondary">عضو منذ {{ $user->created_at->format('Y-m-d') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $roleNames = ['admin' => 'مدير', 'secretary' => 'سكرتارية', 'teacher' => 'معلم', 'parent' => 'ولي أمر'];
                                $roleColors = ['admin' => 'bg-purple-100 text-purple-700', 'secretary' => 'bg-blue-100 text-blue-700', 'teacher' => 'bg-green-100 text-green-700', 'parent' => 'bg-orange-100 text-orange-700'];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-gray-100' }}">
                                {{ $roleNames[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-text-secondary">{{ $user->phone }}</td>
                        <td class="px-6 py-4">
                            <code class="bg-bg px-2 py-1 rounded border border-border text-primary font-bold">{{ $user->access_code }}</code>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.academic.users.edit', $user) }}" class="p-2 text-text-secondary hover:text-primary transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.academic.users.destroy', $user) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-text-secondary hover:text-danger transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-text-secondary">لا يوجد مستخدمون مطابقون للبحث.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<div class="mt-6">
    {{ $users->links() }}
</div>

@if($archived->isNotEmpty())
    <div class="mt-12">
        <h3 class="text-lg font-bold text-text-primary mb-4">الأرشيف (المستخدمون المحذوفون)</h3>
        <x-card class="p-0 overflow-hidden opacity-75">
            <table class="w-full text-sm text-right">
                <tbody class="divide-y divide-border">
                    @foreach($archived as $user)
                        <tr class="bg-bg/20">
                            <td class="px-6 py-4 font-medium text-text-secondary">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-xs text-text-secondary">{{ $user->role }}</td>
                            <td class="px-6 py-4 text-left">
                                <form action="{{ route('admin.academic.users.restore', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-primary text-sm font-bold hover:underline">استعادة</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    </div>
@endif
@endsection
