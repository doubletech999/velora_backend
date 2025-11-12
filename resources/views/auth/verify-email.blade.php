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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            direction: {{ $language === 'ar' ? 'rtl' : 'ltr' }};
        }
        .verification-container {
            max-width: 500px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .logo {
            font-size: 36px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }
        .subtitle {
            font-size: 14px;
            color: #666666;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 20px;
            text-align: {{ $language === 'ar' ? 'right' : 'left' }};
        }
        p {
            font-size: 16px;
            color: #555555;
            margin-bottom: 15px;
            text-align: {{ $language === 'ar' ? 'right' : 'left' }};
            line-height: 1.6;
        }
        .email-display {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            color: #667eea;
        }
        .resend-form {
            margin-top: 30px;
        }
        .resend-button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.4);
        }
        .resend-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.5);
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .info-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="logo">Velora</div>
        <p class="subtitle">{{ $language === 'ar' ? 'اكتشف فلسطين بطريقة جديدة' : 'Discover Palestine in a New Way' }}</p>
        
        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success">
                {{ $language === 'ar' ? 'تم إرسال رابط التحقق الجديد إلى بريدك الإلكتروني.' : 'A new verification link has been sent to your email address.' }}
            </div>
        @endif
        
        @if (session('verified'))
            <div class="alert alert-success">
                {{ $language === 'ar' ? 'تم تأكيد بريدك الإلكتروني بنجاح!' : 'Your email has been successfully verified!' }}
            </div>
        @else
            <div class="info-icon">📧</div>
            <h1>{{ $language === 'ar' ? 'تحقق من بريدك الإلكتروني' : 'Verify Your Email' }}</h1>
            
            @if($language === 'ar')
                <p>مرحباً <strong>{{ $user->name }}</strong>!</p>
                <p>شكراً لك على التسجيل في Velora. يرجى التحقق من بريدك الإلكتروني للبدء.</p>
                <p>لقد أرسلنا رابط التحقق إلى:</p>
            @else
                <p>Hello <strong>{{ $user->name }}</strong>!</p>
                <p>Thanks for signing up for Velora. Please verify your email to get started.</p>
                <p>We've sent a verification link to:</p>
            @endif
            
            <div class="email-display">{{ $user->email }}</div>
            
            @if($language === 'ar')
                <p>إذا لم تستلم البريد الإلكتروني، يمكنك طلب إرسال رابط جديد بالضغط على الزر أدناه:</p>
            @else
                <p>If you didn't receive the email, you can request a new verification link by clicking the button below:</p>
            @endif
            
            <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                @csrf
                <button type="submit" class="resend-button">
                    {{ $language === 'ar' ? 'إعادة إرسال رابط التحقق' : 'Resend Verification Link' }}
                </button>
            </form>
        @endif
    </div>
</body>
</html>


