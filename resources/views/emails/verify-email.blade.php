<!DOCTYPE html>
<html lang="{{ $language === 'ar' ? 'ar' : 'en' }}" dir="{{ $language === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $language === 'ar' ? 'تأكيد البريد الإلكتروني' : 'Email Verification' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            direction: {{ $language === 'ar' ? 'rtl' : 'ltr' }};
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .email-header .subtitle {
            font-size: 16px;
            opacity: 0.95;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body h2 {
            font-size: 22px;
            color: #667eea;
            margin-bottom: 20px;
            text-align: {{ $language === 'ar' ? 'right' : 'left' }};
        }
        .email-body p {
            font-size: 16px;
            margin-bottom: 15px;
            text-align: {{ $language === 'ar' ? 'right' : 'left' }};
        }
        .verify-button {
            display: inline-block;
            margin: 30px 0;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.5);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 30px 0;
        }
        .email-footer {
            padding: 30px;
            background-color: #f9f9f9;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }
        .email-footer p {
            margin-bottom: 10px;
        }
        .bilingual-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .bilingual-section h3 {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 15px;
            text-align: {{ $language === 'ar' ? 'right' : 'left' }};
        }
        .alternate-link {
            font-size: 14px;
            color: #666666;
            word-break: break-all;
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-body {
                padding: 30px 20px !important;
            }
            .verify-button {
                padding: 14px 30px !important;
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Velora</h1>
            <p class="subtitle">{{ $language === 'ar' ? 'اكتشف فلسطين بطريقة جديدة' : 'Discover Palestine in a New Way' }}</p>
        </div>
        
        <div class="email-body">
            @if($language === 'ar')
                <h2>مرحباً {{ $user->name }}!</h2>
                <p>شكراً لك على التسجيل في تطبيق Velora. نحن سعداء بانضمامك إلينا.</p>
                <p>لبدء استخدام حسابك والاستمتاع بجميع الميزات، يرجى تأكيد عنوان بريدك الإلكتروني بالضغط على الزر أدناه:</p>
            @else
                <h2>Hello {{ $user->name }}!</h2>
                <p>Thank you for registering with Velora. We're excited to have you on board!</p>
                <p>To start using your account and enjoy all the features, please verify your email address by clicking the button below:</p>
            @endif
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    {{ $language === 'ar' ? 'تأكيد البريد الإلكتروني' : 'Verify Email Address' }}
                </a>
            </div>
            
            <div class="alternate-link">
                @if($language === 'ar')
                    <p><strong>إذا لم يعمل الزر أعلاه، يمكنك نسخ والصق هذا الرابط في المتصفح:</strong></p>
                @else
                    <p><strong>If the button above doesn't work, you can copy and paste this link into your browser:</strong></p>
                @endif
                <p style="word-break: break-all; margin-top: 10px;">{{ $verificationUrl }}</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="bilingual-section">
                @if($language === 'ar')
                    <h3>English Version</h3>
                    <p>Hello {{ $user->name }}!</p>
                    <p>Thank you for registering with Velora. To complete your registration, please verify your email address by clicking the button above or the link below.</p>
                    <p><strong>This link will expire in 60 minutes.</strong></p>
                    <p>If you didn't create an account with Velora, please ignore this email.</p>
                @else
                    <h3>النسخة العربية</h3>
                    <p>مرحباً {{ $user->name }}!</p>
                    <p>شكراً لك على التسجيل في تطبيق Velora. لإكمال عملية التسجيل، يرجى تأكيد عنوان بريدك الإلكتروني بالضغط على الزر أعلاه أو الرابط أدناه.</p>
                    <p><strong>سينتهي صلاحية هذا الرابط خلال 60 دقيقة.</strong></p>
                    <p>إذا لم تقم بإنشاء حساب في Velora، يرجى تجاهل هذا البريد الإلكتروني.</p>
                @endif
            </div>
        </div>
        
        <div class="email-footer">
            <p><strong>Velora</strong> - {{ $language === 'ar' ? 'اكتشف فلسطين بطريقة جديدة' : 'Discover Palestine in a New Way' }}</p>
            <p>{{ $language === 'ar' ? 'جميع الحقوق محفوظة © ' . date('Y') : 'All rights reserved © ' . date('Y') }}</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999999;">
                {{ $language === 'ar' ? 'إذا واجهتك أي مشكلة، يرجى التواصل مع فريق الدعم.' : 'If you have any issues, please contact our support team.' }}
            </p>
        </div>
    </div>
</body>
</html>


