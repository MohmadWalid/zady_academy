@props(['status'])

@php
    $colors = [
        'paid' => 'bg-success-bg text-success',
        'approved' => 'bg-success-bg text-success',
        'pending' => 'bg-warning-bg text-warning',
        'unpaid' => 'bg-danger-bg text-danger',
        'rejected' => 'bg-danger-bg text-danger',
    ];
    $labels = [
        'paid' => 'مدفوع',
        'approved' => 'مقبول',
        'pending' => 'قيد المراجعة',
        'unpaid' => 'غير مدفوع',
        'rejected' => 'مرفوض',
    ];
    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-600';
    $label = $labels[$status] ?? $status;
@endphp

<span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
    {{ $label }}
</span>
