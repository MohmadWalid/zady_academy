@extends('layouts.auth')

@section('content')
<div class="w-full max-w-[360px]">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary mb-2">ZADY</h1>
        <p class="text-text-secondary">تسجيل الدخول للنظام</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
            @csrf
            
            <div>
                <label for="access_code" class="block text-sm font-medium text-text-primary mb-2">كود الدخول</label>
                <div class="relative">
                    <input type="text" id="access_code" name="access_code" value="{{ old('access_code') }}" placeholder="ZADY-XXXX" 
                           class="w-full h-[48px] px-4 border rounded-xl font-mono text-center text-lg tracking-wider focus:ring-2 focus:ring-primary focus:border-primary @error('access_code') border-danger @else border-border @enderror" 
                           required autofocus dir="ltr">
                </div>
                @error('access_code')
                    <p class="mt-2 text-sm text-danger flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        الكود غير صحيح، تواصل مع الإدارة
                    </p>
                @enderror
            </div>

            <button type="submit" class="w-full h-[48px] bg-primary hover:bg-primary-dark text-white rounded-xl font-medium text-lg transition-colors shadow-sm mt-2">
                دخول
            </button>
        </form>
    </x-card>
</div>
@endsection
