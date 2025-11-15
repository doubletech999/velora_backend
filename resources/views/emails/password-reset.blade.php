<!DOCTYPE html>
<html lang="{{ $language === 'ar' ? 'ar' : 'en' }}" dir="{{ $language === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $language === 'ar' ? 'إعادة تعيين كلمة المرور' : 'Password Reset' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        h2 {
            color: #333;
            margin-top: 0;
            {{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}
        }
        p {
            {{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}
            margin-bottom: 15px;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background-color: #4CAF50;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            {{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}
        }
        .button:hover {
            background-color: #45a049;
        }
        .url-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            {{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            {{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}
            font-size: 12px;
            color: #666;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            {{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🌍 Velora</div>
        </div>

        @if($language === 'ar')
            <h2>إعادة تعيين كلمة المرور</h2>
            
            <p>مرحباً،</p>
            
            <p>لقد تلقيت هذا البريد الإلكتروني لأننا تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك.</p>
            
            <p>اضغط على الزر أدناه لإعادة تعيين كلمة المرور:</p>
            
            <div style="{{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}">
                <a href="{{ $resetUrl }}" class="button">إعادة تعيين كلمة المرور</a>
            </div>
            
            <p>أو انسخ والصق الرابط التالي في المتصفح:</p>
            
            <div class="url-box">
                {{ $resetUrl }}
            </div>
            
            <p>هذا الرابط سينتهي خلال 60 دقيقة.</p>
            
            <p>إذا لم تطلب إعادة تعيين كلمة المرور، لا حاجة لاتخاذ أي إجراء.</p>
            
            <div class="footer">
                <p>شكراً،<br><strong>فريق Velora</strong></p>
            </div>
        @else
            <h2>Password Reset</h2>
            
            <p>Hello,</p>
            
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <p>Click the button below to reset your password:</p>
            
            <div style="{{ $language === 'ar' ? 'text-align: right;' : 'text-align: left;' }}">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>
            
            <p>Or copy and paste the following link into your browser:</p>
            
            <div class="url-box">
                {{ $resetUrl }}
            </div>
            
            <p>This link will expire in 60 minutes.</p>
            
            <p>If you did not request a password reset, no further action is required.</p>
            
            <div class="footer">
                <p>Thank you,<br><strong>The Velora Team</strong></p>
            </div>
        @endif
    </div>
</body>
</html>


