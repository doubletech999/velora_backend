<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMNotificationService
{
    /**
     * Send FCM notification
     */
    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        $serverKey = config('services.fcm.server_key');

        if (!$serverKey) {
            Log::warning('FCM Server Key is not configured');
            return false;
        }

        if (!$fcmToken) {
            Log::warning('FCM token is missing');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
                'priority' => 'high',
            ]);

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', [
                    'token' => substr($fcmToken, 0, 20) . '...',
                    'title' => $title
                ]);
                return true;
            } else {
                Log::error('FCM notification failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('FCM notification exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmationNotification(Booking $booking)
    {
        $user = $booking->user;

        if (!$user || !$user->fcm_token) {
            Log::warning("User {$user->id} does not have FCM token");
            return false;
        }

        // Get path/site name
        $pathName = 'الرحلة';
        if ($booking->site_id) {
            $site = \App\Models\Site::find($booking->site_id);
            if ($site) {
                $pathName = $site->name_ar ?? $site->name ?? 'الرحلة';
            }
        } elseif ($booking->path_id) {
            $site = \App\Models\Site::where('id', $booking->path_id)->first();
            if ($site) {
                $pathName = $site->name_ar ?? $site->name ?? 'الرحلة';
            }
        }

        $notificationData = [
            'type' => 'trip_accepted',
            'trip_id' => (string) ($booking->trip_id ?? $booking->id),
            'trip_name' => $pathName,
            'booking_id' => (string) $booking->id,
            'booking_date' => $booking->booking_date->format('Y-m-d'),
        ];

        return $this->sendNotification(
            $user->fcm_token,
            'تم قبول حجزك!',
            'تم قبول حجزك في ' . $pathName . '. استمتع برحلتك!',
            $notificationData
        );
    }

    /**
     * Send booking rejection notification
     */
    public function sendBookingRejectionNotification(Booking $booking)
    {
        $user = $booking->user;

        if (!$user || !$user->fcm_token) {
            return false;
        }

        // Get path/site name
        $pathName = 'الرحلة';
        if ($booking->site_id) {
            $site = \App\Models\Site::find($booking->site_id);
            if ($site) {
                $pathName = $site->name_ar ?? $site->name ?? 'الرحلة';
            }
        } elseif ($booking->path_id) {
            $site = \App\Models\Site::where('id', $booking->path_id)->first();
            if ($site) {
                $pathName = $site->name_ar ?? $site->name ?? 'الرحلة';
            }
        }

        $notificationData = [
            'type' => 'trip_rejected',
            'trip_id' => (string) ($booking->trip_id ?? $booking->id),
            'trip_name' => $pathName,
            'booking_id' => (string) $booking->id,
        ];

        return $this->sendNotification(
            $user->fcm_token,
            'تم رفض حجزك',
            'نأسف، تم رفض حجزك في ' . $pathName . '.',
            $notificationData
        );
    }

    /**
     * Send trip accepted notification (for compatibility)
     */
    public function notifyTripAccepted(User $user, $trip)
    {
        if (!$user->fcm_token) {
            return false;
        }

        $tripName = is_object($trip) ? ($trip->trip_name ?? 'الرحلة') : 'الرحلة';

        $notificationData = [
            'type' => 'trip_accepted',
            'trip_id' => (string) (is_object($trip) ? $trip->id : $trip),
            'trip_name' => $tripName,
        ];

        return $this->sendNotification(
            $user->fcm_token,
            'تم قبول حجزك!',
            'تم قبول حجزك في ' . $tripName . '. استمتع برحلتك!',
            $notificationData
        );
    }
}


