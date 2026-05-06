@extends('layouts.app')

@section('title', 'الحلقات')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <x-page-header title="الحلقات القرآنية" subtitle="إدارة حلقات التحفيظ" />
    
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <div class="w-full sm:w-72">
            <x-search-bar name="search" placeholder="بحث باسم الحلقة أو المعلم..." />
        </div>
        <a href="{{ route('secretary.academic.groups.create') }}" class="bg-primary text-white px-6 py-2 rounded-xl font-bold hover:bg-primary/90 transition-all flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            إضافة حلقة
        </a>
    </div>
</div>

@if($groups->isEmpty())
    <x-card>
        <x-empty-state 
            icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>'
            title="لا يوجد حلقات بعد" 
            subtitle="لم يتم إنشاء أي حلقات قرآنية حتى الآن." 
            actionLabel="إنشاء أول حلقة" 
            actionRoute="{{ route('secretary.academic.groups.create') }}" />
    </x-card>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($groups as $group)
            <a href="{{ route('secretary.academic.groups.show', $group) }}" class="block hover:-translate-y-1 transition-transform">
                <x-group-card :group="$group" :studentCount="$group->active_enrollments_count" />
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $groups->links() }}
    </div>
@endif

@if($archived->isNotEmpty())
    <div class="mt-12 mb-4 border-t border-border pt-8">
        <h3 class="text-lg font-bold text-text-primary flex items-center gap-2">
            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            الأرشيف (الحلقات المحذوفة)
        </h3>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-60">
        @foreach($archived as $oldGroup)
            <div class="relative group">
                <x-group-card :group="$oldGroup" studentCount="0" />
                <div class="absolute inset-0 bg-surface/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-[14px]">
                    <form action="{{ route('secretary.academic.groups.restore', $oldGroup->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg font-bold shadow-lg">استعادة</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
