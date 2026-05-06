@extends('layouts.app')

@section('title', 'تفاصيل الحلقة')

@section('content')
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('secretary.academic.groups.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </a>
    <x-page-header title="{{ $group->name }}" subtitle="تفاصيل الحلقة القرآنية" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 flex flex-col gap-6">
        <x-card>
            <h3 class="font-bold text-text-primary mb-4 pb-2 border-b border-border">معلومات الحلقة</h3>
            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-text-secondary">المعلم</span>
                    <span class="text-text-primary font-bold">{{ $group->teacher->name ?? 'غير محدد' }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-text-secondary">النوع</span>
                    <span class="text-text-primary">
                        @if($group->type === 'general') عامة @elseif($group->type === 'private') خاصة @else دورة @endif
                    </span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-text-secondary">السعر الشهري</span>
                    <span class="text-primary font-bold">{{ number_format($group->monthly_price) }} جنيه</span>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-text-primary">مواعيد الحلقة</h3>
                @if($group->sessions->count() < 2)
                    <button onclick="document.getElementById('add-session-modal').classList.remove('hidden')" class="text-xs text-primary font-bold">+ إضافة موعد</button>
                @endif
            </div>
            
            @if($group->sessions->isEmpty())
                <p class="text-xs text-text-secondary text-center py-2">لم يتم تحديد مواعيد بعد.</p>
            @else
                <div class="flex flex-col gap-2">
                    @foreach($group->sessions as $session)
                        <div class="flex justify-between items-center p-3 rounded-lg bg-bg/50 border border-border">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-text-primary">{{ match($session->day) { 'Saturday' => 'السبت', 'Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', default => $session->day } }}</span>
                                <span class="text-sm text-text-secondary">{{ \Carbon\Carbon::parse($session->time)->format('g:i A') }}</span>
                            </div>
                            <form action="{{ route('secretary.academic.groups.sessions.destroy', [$group, $session]) }}" method="POST" onsubmit="return confirm('حذف هذا الموعد؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-text-secondary hover:text-error">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <div class="flex flex-col gap-3">
            <a href="{{ route('secretary.academic.groups.edit', $group) }}" class="w-full bg-surface border border-border text-text-primary py-3 rounded-xl font-bold hover:bg-bg transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                تعديل المجموعة
            </a>
            <form action="{{ route('secretary.academic.groups.destroy', $group) }}" method="POST" onsubmit="return confirm('نقل للمجموعة للأرشيف؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-danger-bg/20 text-error border border-error/10 py-3 rounded-xl font-bold hover:bg-danger-bg/30 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    حذف المجموعة
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2 flex flex-col gap-6">
        <x-card>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-text-primary">الطلاب المسجلين</h3>
                <span class="text-sm text-text-secondary">{{ $group->activeEnrollments->count() }} طالب</span>
            </div>

            @if($group->activeEnrollments->isEmpty())
                <x-empty-state icon='<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>' title="لا يوجد طلاب" subtitle="هذه الحلقة خالية من الطلاب حالياً." />
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($group->activeEnrollments as $enrollment)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-bg border border-border">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold">
                                    {{ mb_substr($enrollment->student->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-text-primary text-sm">{{ $enrollment->student->name }}</h4>
                                    <p class="text-xs text-text-secondary">{{ $enrollment->student->phone }}</p>
                                </div>
                            </div>
                            <a href="{{ route('secretary.academic.students.show', $enrollment->student) }}" class="text-xs text-primary font-bold">الملف →</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-text-primary">سجل الحضور الأخير</h3>
                <a href="{{ route('secretary.attendance.show', $group) }}" class="text-sm font-bold text-primary">عرض الكل</a>
            </div>
            @if($group->attendance->isEmpty())
                <p class="text-text-secondary text-center py-4">لا يوجد سجلات حضور لهذه المجموعة.</p>
            @else
                <div class="space-y-3">
                    @foreach($group->attendance->groupBy('date')->sortByDesc(fn($g, $key) => $key)->take(3) as $date => $records)
                        <div class="flex justify-between items-center p-3 rounded-lg border border-border bg-bg/20">
                            <span class="text-sm font-bold text-text-primary">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</span>
                            <div class="flex gap-4 text-xs font-medium">
                                <span class="text-success">حضور: {{ $records->where('present', true)->count() }}</span>
                                <span class="text-error">غياب: {{ $records->where('present', false)->count() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
</div>

<!-- Add Session Modal (Hidden) -->
<div id="add-session-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-surface rounded-2xl w-full max-w-md shadow-2xl p-6">
        <h3 class="text-xl font-bold text-text-primary mb-6">إضافة موعد للحلقة</h3>
        <form action="{{ route('secretary.academic.groups.sessions.store', $group) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">اليوم</label>
                <select name="day" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                    <option value="Saturday">السبت</option>
                    <option value="Sunday">الأحد</option>
                    <option value="Monday">الاثنين</option>
                    <option value="Tuesday">الثلاثاء</option>
                    <option value="Wednesday">الأربعاء</option>
                    <option value="Thursday">الخميس</option>
                    <option value="Friday">الجمعة</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">الوقت</label>
                <input type="time" name="time" required class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary text-white h-12 rounded-xl font-bold hover:bg-primary-dark transition-colors">إضافة</button>
                <button type="button" onclick="document.getElementById('add-session-modal').classList.add('hidden')" class="flex-1 bg-bg text-text-secondary h-12 rounded-xl font-medium border border-border">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection
