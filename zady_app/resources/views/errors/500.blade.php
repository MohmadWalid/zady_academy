<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>500 — خطأ في الخادم</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            direction: rtl;
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background: #F8F9FB;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #111827;
        }
        .wrap       { text-align: center; max-width: 380px; }
        .code       { font-size: 72px; font-weight: 700; color: #E4E7EC; line-height: 1; margin-bottom: 16px; }
        .title      { font-size: 20px; font-weight: 700; margin-bottom: 10px; }
        .body       { font-size: 15px; color: #6B7280; line-height: 1.6; margin-bottom: 28px; }
        .btn {
            display: inline-block;
            padding: 12px 28px;
            background: #3B6EF8;
            color: #fff;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            transition: background 150ms;
        }
        .btn:hover { background: #2A52C9; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="code">500</div>
        <h1 class="title">حدث خطأ غير متوقع</h1>
        <p class="body">
            واجه النظام مشكلة أثناء معالجة طلبك. تم تسجيل الخطأ. يرجى المحاولة مجدداً أو التواصل مع الإدارة.
        </p>
        <a href="{{ url('/') }}" class="btn">العودة للصفحة الرئيسية</a>
    </div>
</body>
</html>
