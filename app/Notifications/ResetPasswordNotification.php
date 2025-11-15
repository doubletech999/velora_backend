<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = $this->resetUrl($notifiable);
        
        // Determine user's preferred language or default to English
        $language = $notifiable->language ?? 'en';
        
        if ($language === 'ar') {
            return (new MailMessage)
                ->subject('إعادة تعيين كلمة المرور - Velora | Password Reset - Velora')
                ->view('emails.password-reset', [
                    'resetUrl' => $resetUrl,
                    'language' => 'ar'
                ]);
        }
        
        return (new MailMessage)
            ->subject('Password Reset - Velora | إعادة تعيين كلمة المرور - Velora')
            ->view('emails.password-reset', [
                'resetUrl' => $resetUrl,
                'language' => 'en'
            ]);
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl($notifiable)
    {
        // For mobile app - use deep link
        // للتطبيق المحمول - استخدم deep link
        $token = $this->token;
        $email = urlencode($notifiable->email);
        
        // Option 1: Web URL (opens in browser, then redirects to app)
        // الخيار 1: رابط ويب (يفتح في المتصفح، ثم يوجه للتطبيق)
        // return url("https://your-app-domain.com/reset-password?email={$email}&token={$token}");
        
        // Option 2: Deep link (directly opens app)
        // الخيار 2: رابط عميق (يفتح التطبيق مباشرة)
        return "velora://reset-password?email={$email}&token={$token}";
        
        // Option 3: Universal link (iOS) / App link (Android)
        // الخيار 3: رابط عالمي (iOS) / رابط التطبيق (Android)
        // return url("https://your-app-domain.com/reset-password?email={$email}&token={$token}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}


