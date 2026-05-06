<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zady Academy - تأكيد الإيصال</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text-primary font-sans antialiased flex flex-col items-center justify-center min-h-screen p-4 text-center">

    <div class="w-24 h-24 bg-success-bg text-success rounded-full flex items-center justify-center mb-6 shadow-sm border border-success/20">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    </div>

    <h1 class="text-3xl font-bold text-text-primary mb-3">تم استلام إيصالك</h1>
    <p class="text-lg text-text-secondary mb-8">وهو قيد المراجعة من قبل الإدارة، سيتم تحديث حالة اشتراكك قريباً.</p>

    <a href="{{ route('parent.dashboard') }}" class="px-8 h-[48px] flex items-center justify-center bg-primary text-white rounded-xl font-medium text-lg hover:bg-primary-dark transition-colors shadow-sm">
        العودة للرئيسية
    </a>

</body>
</html>
