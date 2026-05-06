@extends('layouts.app')

@section('title', 'الطلاب')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="الطلاب" subtitle="إدارة جميع طلاب الأكاديمية" />
    
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <div class="w-full sm:w-72">
            <x-search-bar name="search" placeholder="بحث باسم الطالب أو الكود..." />
        </div>
        <a href="{{ route('secretary.academic.students.create') }}" class="bg-primary text-white px-6 py-2 rounded-xl font-bold hover:bg-primary/90 transition-all flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            إضافة طالب
        </a>
    </div>
</div>

<x-card class="overflow-hidden">
    @if($students->isEmpty())
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
            title="لا يوجد طلاب بعد" 
            subtitle="لم يتم إضافة أي طلاب للنظام حتى الآن." 
            actionLabel="إضافة طالب جديد" 
            actionRoute="{{ route('secretary.academic.students.create') }}" />
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-bg text-text-secondary text-sm">
                        <th class="px-6 py-4 font-bold border-b border-border">اسم الطالب</th>
                        <th class="px-6 py-4 font-bold border-b border-border">ولي الأمر</th>
                        <th class="px-6 py-4 font-bold border-b border-border">رقم الهاتف</th>
                        <th class="px-6 py-4 font-bold border-b border-border">العمر</th>
                        <th class="px-6 py-4 font-bold border-b border-border">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($students as $student)
                        <tr class="hover:bg-bg transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('secretary.academic.students.show', $student) }}" class="font-bold text-text-primary hover:text-primary transition-colors">
                                    {{ $student->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-text-secondary">
                                {{ $student->parent->name ?? '---' }}
                            </td>
                            <td class="px-6 py-4 text-text-secondary dir-ltr">
                                {{ $student->phone ?? '---' }}
                            </td>
                            <td class="px-6 py-4 text-text-secondary">
                                {{ $student->age }} سنة
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('secretary.academic.students.edit', $student) }}" class="p-2 text-text-secondary hover:text-primary transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('secretary.academic.students.destroy', $student) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-text-secondary hover:text-error transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $students->links() }}
            </div>
        @endif
    @endif
</x-card>

@if($archived->isNotEmpty())
    <div class="mt-12 mb-4">
        <h3 class="text-lg font-bold text-text-primary flex items-center gap-2">
            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            الأرشيف (الطلاب المحذوفين)
        </h3>
    </div>
    
    <x-card class="bg-bg/50">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="text-text-secondary text-sm">
                        <th class="px-6 py-4 font-bold">الاسم</th>
                        <th class="px-6 py-4 font-bold">تاريخ الحذف</th>
                        <th class="px-6 py-4 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($archived as $oldStudent)
                        <tr>
                            <td class="px-6 py-4 text-text-secondary">{{ $oldStudent->name }}</td>
                            <td class="px-6 py-4 text-text-secondary">{{ $oldStudent->deleted_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('secretary.academic.students.restore', $oldStudent->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-primary hover:underline text-sm font-bold">استعادة</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
@endif

@endsection
