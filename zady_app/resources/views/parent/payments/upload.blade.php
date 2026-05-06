@extends('layouts.app')

@section('title', 'رفع إيصال تحويل')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('parent.payments.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-border text-text-secondary hover:text-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        <x-page-header title="تأكيد تحويل بنكي" subtitle="قم برفع صورة إيصال التحويل لتأكيد دفع الاشتراك" />
    </div>

    @if($unpaidSubscriptions->isEmpty())
        <x-card>
            <x-empty-state 
                icon='<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                title="لا يوجد اشتراكات مستحقة" 
                subtitle="جميع اشتراكات أبنائك مدفوعة حالياً. شكراً لك!" />
        </x-card>
    @else
        <x-card>
            <form action="{{ route('parent.payments.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">اختر الاشتراك المراد دفعه</label>
                    <select name="subscription_id" required onchange="updateAmount(this)" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none">
                        <option value="" data-amount="0">اختر الطالب والحلقة...</option>
                        @foreach($unpaidSubscriptions as $sub)
                            <option value="{{ $sub->id }}" data-amount="{{ $sub->group->monthly_price }}">
                                {{ $sub->student->name }} - {{ $sub->group->name }} (شهر {{ $sub->month }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">المبلغ المحول (جنيه)</label>
                    <input type="number" name="amount" id="amount-input" step="0.01" class="w-full h-[48px] px-4 border border-border rounded-xl bg-bg focus:ring-primary focus:border-primary outline-none" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">صورة الإيصال</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-border border-dashed rounded-xl bg-bg hover:bg-surface transition-colors cursor-pointer relative overflow-hidden" onclick="document.getElementById('proof').click()">
                        <div class="space-y-1 text-center">
                            <svg id="upload-icon" class="mx-auto h-12 w-12 text-text-secondary" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <img id="preview" class="mx-auto h-32 hidden object-cover rounded-lg mb-2" />
                            <div class="flex text-sm text-text-primary justify-center">
                                <span class="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary-dark">
                                    <span id="upload-text">اضغط لرفع صورة الإيصال</span>
                                    <input id="proof" name="proof" type="file" class="sr-only" accept="image/*" required onchange="previewImage(this)">
                                </span>
                            </div>
                            <p class="text-xs text-text-secondary">PNG, JPG حتى 5MB</p>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full h-[54px] bg-primary text-white font-bold rounded-2xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 mt-2">
                    إرسال للتأكيد
                </button>
            </form>
        </x-card>
    @endif
</div>

<script>
    function updateAmount(select) {
        const amount = select.options[select.selectedIndex].getAttribute('data-amount');
        document.getElementById('amount-input').value = amount;
    }

    function previewImage(input) {
        const preview = document.getElementById('preview');
        const icon = document.getElementById('upload-icon');
        const text = document.getElementById('upload-text');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                icon.classList.add('hidden');
                text.innerText = 'تغيير الصورة';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
