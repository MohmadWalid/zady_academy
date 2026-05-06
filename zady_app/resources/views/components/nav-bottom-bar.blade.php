<div class="fixed bottom-0 inset-x-0 h-[60px] bg-surface border-t border-border flex justify-around items-center px-2 z-40">
    @php
        $role = auth()->user()->role ?? 'admin';
        $prefix = $role;
    @endphp

    <a href="{{ route($prefix . '.dashboard') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs($prefix . '.dashboard') ? 'text-primary' : 'text-text-secondary' }}">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        <span class="text-[10px] font-medium">الرئيسية</span>
    </a>

    @if(in_array($role, ['admin', 'secretary']))
        <a href="{{ route($prefix . '.academic.students.index') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs($prefix . '.academic.students.*') ? 'text-primary' : 'text-text-secondary' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="text-[10px] font-medium">الطلاب</span>
        </a>
    @endif

    @if(in_array($role, ['admin', 'secretary', 'teacher']))
        <a href="{{ route($prefix . '.attendance.index') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs($prefix . '.attendance.*') ? 'text-primary' : 'text-text-secondary' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            <span class="text-[10px] font-medium">التحضير</span>
        </a>
    @endif

    <form method="POST" action="{{ route('logout') }}" class="flex h-full">
        @csrf
        <button type="submit" class="flex flex-col items-center justify-center w-16 h-full text-text-secondary">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="text-[10px] font-medium">خروج</span>
        </button>
    </form>
</div>
