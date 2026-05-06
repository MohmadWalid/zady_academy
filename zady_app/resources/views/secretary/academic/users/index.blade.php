@extends('layouts.app')

@section('title', 'أولياء الأمور')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="أولياء الأمور" subtitle="إدارة حسابات وأكواد دخول أولياء الأمور" />
</div>

<x-card class="mb-8">
    <form action="{{ route('secretary.academic.users.index') }}" method="GET">
        <x-search-bar name="search" value="{{ request('search') }}" placeholder="ابحث باسم ولي الأمر، هاتفه، أو كود الطالب..." />
    </form>
</x-card>

<x-card class="p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="text-xs text-text-secondary bg-bg uppercase border-b border-border">
                <tr>
                    <th class="px-6 py-4 rounded-tr-xl">ولي الأمر</th>
                    <th class="px-6 py-4">الهاتف</th>
                    <th class="px-6 py-4">كود الدخول</th>
                    <th class="px-6 py-4 rounded-tl-xl text-left">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($users as $user)
                    <tr class="hover:bg-bg transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-text-primary">{{ $user->name }}</div>
                            <div class="text-xs text-text-secondary">انضم في {{ $user->created_at->format('Y-m-d') }}</div>
                        </td>
                        <td class="px-6 py-4 text-text-secondary">{{ $user->phone }}</td>
                        <td class="px-6 py-4 font-bold text-primary">{{ $user->access_code }}</td>
                        <td class="px-6 py-4 text-left">
                            <a href="{{ route('secretary.academic.users.show', $user) }}" class="text-sm font-bold text-primary hover:underline">عرض التفاصيل ←</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-text-secondary">لا يوجد أولياء أمور مطابقين للبحث.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<div class="mt-6">
    {{ $users->links() }}
</div>
@endsection
