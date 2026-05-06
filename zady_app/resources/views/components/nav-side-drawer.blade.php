<div class="w-[240px] h-full bg-surface border-l border-border flex flex-col pt-8 pb-4">
    <div class="px-6 mb-8">
        <h2 class="text-primary font-bold text-2xl">ZADY</h2>
        <p class="text-text-secondary text-xs mt-1">Quran Academy</p>
    </div>

    @php
        $role = auth()->user()->role ?? 'admin';
        $prefix = $role;
    @endphp

    <nav class="flex-1 px-4 flex flex-col gap-2 overflow-y-auto">
        <a href="{{ route($prefix . '.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs($prefix . '.dashboard') ? 'bg-primary-light text-primary font-bold' : 'text-text-secondary hover:bg-bg' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>الرئيسية</span>
        </a>

        @if(in_array($role, ['admin', 'secretary']))
            <a href="{{ route($prefix . '.academic.students.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs($prefix . '.academic.students.*') ? 'bg-primary-light text-primary font-bold' : 'text-text-secondary hover:bg-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>الطلاب</span>
            </a>
            <a href="{{ route($prefix . '.academic.groups.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs($prefix . '.academic.groups.*') ? 'bg-primary-light text-primary font-bold' : 'text-text-secondary hover:bg-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>الحلقات</span>
            </a>
        @endif

        @if($role === 'admin')
            <a href="{{ route($prefix . '.academic.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs($prefix . '.academic.users.*') ? 'bg-primary-light text-primary font-bold' : 'text-text-secondary hover:bg-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span>الموظفين</span>
            </a>
        @endif

        @if(in_array($role, ['admin', 'secretary', 'teacher']))
            <a href="{{ route($prefix . '.attendance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs($prefix . '.attendance.*') ? 'bg-primary-light text-primary font-bold' : 'text-text-secondary hover:bg-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span>التحضير</span>
            </a>
        @endif

        @if(in_array($role, ['admin', 'secretary']))
            <a href="{{ route($prefix . '.payments.pending') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs($prefix . '.payments.*') ? 'bg-primary-light text-primary font-bold' : 'text-text-secondary hover:bg-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>المدفوعات</span>
            </a>
        @endif

        @if($role === 'parent')
            <a href="{{ route('parent.payments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('parent.payments.*') ? 'bg-primary-light text-primary font-bold' : 'text-text-secondary hover:bg-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>المدفوعات</span>
            </a>
        @endif
    </nav>

    <div class="px-4 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-text-secondary hover:bg-danger-bg hover:text-danger transition-colors font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>تسجيل الخروج</span>
            </button>
        </form>
    </div>
</div>
