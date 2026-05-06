@extends('layouts.app')

@section('title', 'تفاصيل الطالب')

@section('content')
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.academic.students.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </a>
    <x-page-header title="تفاصيل الطالب" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sidebar -->
    <div class="lg:col-span-1 flex flex-col gap-6">
        <x-card>
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full bg-primary-light text-primary flex items-center justify-center text-3xl font-bold mb-4">
                    {{ mb_substr($student->name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-text-primary mb-1">{{ $student->name }}</h2>
                <x-status-badge status="active" />
                
                <div class="mt-6 w-full pt-6 border-t border-border flex flex-col gap-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-text-secondary">ولي الأمر</span>
                        <span class="text-text-primary font-medium">{{ $student->parent->name ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-text-secondary">كود ولي الأمر</span>
                        <x-code-block code="{{ $student->parent->access_code ?? '---' }}" />
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-text-secondary">رقم الهاتف</span>
                        <span class="text-text-primary font-medium dir-ltr">{{ $student->phone ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-text-secondary">العمر</span>
                        <span class="text-text-primary font-medium">{{ $student->age }} سنة</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-text-secondary">تاريخ التسجيل</span>
                        <span class="text-text-primary font-medium">{{ $student->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="font-bold text-xs text-text-secondary uppercase tracking-widest mb-4 pb-2 border-b border-border">بيانات التدقيق (Auditing)</h3>
            <div class="space-y-4">
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] text-text-secondary font-bold uppercase">تم الإنشاء بواسطة</span>
                    <span class="text-xs font-medium text-text-primary">{{ $student->creator->name ?? 'النظام (Seeder)' }}</span>
                    <span class="text-[10px] text-text-secondary">{{ $student->created_at->format('Y-m-d H:i') }}</span>
                </div>
                
                @if($student->updated_by)
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] text-text-secondary font-bold uppercase">آخر تحديث بواسطة</span>
                        <span class="text-xs font-medium text-text-primary">{{ $student->updater->name ?? '---' }}</span>
                        <span class="text-[10px] text-text-secondary">{{ $student->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                @endif
            </div>
        </x-card>

        <div class="flex flex-col gap-3">
            <a href="{{ route('admin.academic.students.edit', $student) }}" class="w-full bg-surface border border-border text-text-primary py-3 rounded-xl font-bold hover:bg-bg transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                تعديل البيانات
            </a>
            <form action="{{ route('admin.academic.students.destroy', $student) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من أرشفة هذا الطالب؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-error/10 text-error py-3 rounded-xl font-bold hover:bg-error hover:text-white transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    نقل للأرشيف
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="lg:col-span-2 flex flex-col gap-6">
        <!-- Enrollments -->
        <x-card>
            <div class="flex justify-between items-center mb-6" x-data="{ showForm: false }">
                <h3 class="text-lg font-bold text-text-primary">الحلقات المشترك بها</h3>
                <button @click="showForm = !showForm" class="text-sm font-bold text-primary hover:underline" x-text="showForm ? 'إلغاء' : '+ إضافة لحلقة'"></button>
                
                <div x-show="showForm" x-cloak class="absolute top-16 left-0 right-0 z-10 p-6 bg-white border border-border rounded-2xl shadow-xl animate-slide-up" style="display: none;">
                    <form action="{{ route('admin.academic.enrollments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-text-primary mb-2">اختر الحلقة</label>
                            @php $groups = \App\Models\Group::whereNotIn('id', $student->activeEnrollments->pluck('group_id'))->get(); @endphp
                            <select name="group_id" class="w-full h-12 px-4 rounded-xl border border-border bg-bg focus:ring-primary focus:border-primary outline-none">
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }} ({{ number_format($g->monthly_price) }} ج.م)</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-primary-dark transition-all">تأكيد الإضافة</button>
                    </form>
                </div>
            </div>
            
            @if($student->activeEnrollments->isEmpty())
                <x-empty-state 
                    icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
                    title="لا يوجد حلقات" 
                    subtitle="الطالب غير مسجل في أي حلقة حالياً." />
            @else
                <div class="space-y-4">
                    @foreach($student->activeEnrollments as $enrollment)
                        <div class="p-4 rounded-xl border border-border bg-bg/30 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                                    {{ mb_substr($enrollment->group->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-text-primary mb-1">{{ $enrollment->group->name }}</h4>
                                    <p class="text-xs text-text-secondary">المعلم: {{ $enrollment->group->teacher->name }} | {{ number_format($enrollment->group->monthly_price) }} ج.م</p>
                                </div>
                            </div>
                            <form action="{{ route('admin.academic.enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء اشتراك الطالب في هذه الحلقة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-text-secondary hover:text-error transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
        
        <!-- Attendance -->
        <x-card>
            <h3 class="text-lg font-bold text-text-primary mb-6">سجل الحضور الأخير</h3>
            @if($student->attendance->isEmpty())
                <p class="text-text-secondary text-center py-4">لا يوجد سجل حضور لهذا الطالب.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="text-text-secondary text-sm">
                                <th class="pb-4 font-bold border-b border-border">التاريخ</th>
                                <th class="pb-4 font-bold border-b border-border">الحلقة</th>
                                <th class="pb-4 font-bold border-b border-border text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($student->attendance->sortByDesc('date')->take(5) as $record)
                                <tr>
                                    <td class="py-4 text-sm text-text-primary font-medium">{{ $record->date->format('Y-m-d') }}</td>
                                    <td class="py-4 text-sm text-text-secondary">{{ $record->group->name ?? '---' }}</td>
                                    <td class="py-4 text-center">
                                        @if($record->present)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-success-bg text-success uppercase">حاضر</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-error/10 text-error uppercase">غائب</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
</div>

<style>
    @keyframes slide-up {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-slide-up {
        animation: slide-up 0.2s ease-out forwards;
    }
</style>
@endsection
