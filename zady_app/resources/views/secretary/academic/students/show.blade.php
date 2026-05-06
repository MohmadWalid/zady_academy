@extends('layouts.app')

@section('title', 'تفاصيل الطالب')

@section('content')
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('secretary.academic.students.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </a>
    <x-page-header title="تفاصيل الطالب" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
        
        <div class="flex flex-col gap-3">
            <a href="{{ route('secretary.academic.students.edit', $student) }}" class="w-full bg-surface border border-border text-text-primary py-3 rounded-xl font-bold hover:bg-bg transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                تعديل البيانات
            </a>
        </div>
    </div>
    
    <div class="lg:col-span-2 flex flex-col gap-6">
        <x-card>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-text-primary">الحلقات المشترك بها</h3>
                <a href="#" class="text-sm font-medium text-primary hover:text-primary-dark">إدارة الاشتراكات</a>
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
                            <div>
                                <h4 class="font-bold text-text-primary mb-1">{{ $enrollment->group->name }}</h4>
                                <p class="text-sm text-text-secondary">المعلم: {{ $enrollment->group->teacher->name }}</p>
                            </div>
                            <div class="text-left">
                                <span class="text-primary font-bold">{{ number_format($enrollment->group->monthly_price) }} جنيه</span>
                                <p class="text-xs text-text-secondary">شهرياً</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
        
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
                                    <td class="py-4 text-sm text-text-primary">{{ $record->date->format('Y-m-d') }}</td>
                                    <td class="py-4 text-sm text-text-secondary">{{ $record->group->name ?? '---' }}</td>
                                    <td class="py-4 text-center">
                                        @if($record->present)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-bg text-success">حاضر</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-error-bg text-error">غائب</span>
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
@endsection
