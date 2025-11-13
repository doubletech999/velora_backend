<?php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(storage_path('app/firebase-credentials.json'));
            
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            $this->messaging = null;
        }
    }

    /**
     * Send notification to a single user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        if (!$this->messaging || !$user->fcm_token) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);
            
            Log::info('Firebase notification sent to user: ' . $user->id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(array $users, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            return false;
        }

        $tokens = [];
        foreach ($users as $user) {
            if ($user->fcm_token) {
                $tokens[] = $user->fcm_token;
            }
        }

        if (empty($tokens)) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $report = $this->messaging->sendMulticast($message, $tokens);
            
            Log::info('Firebase notifications sent. Success: ' . $report->successes()->count() . ', Failures: ' . $report->failures()->count());
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notifications: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers(string $title, string $body, array $data = [])
    {
        $users = User::whereNotNull('fcm_token')->get();
        return $this->sendToUsers($users->toArray(), $title, $body, $data);
    }

    /**
     * Send notification when new route/camping is created
     */
    public function notifyNewRouteOrCamping($site)
    {
        $type = $site->type === 'route' ? 'مسار جديد' : 'تخييم جديد';
        $typeEn = $site->type === 'route' ? 'New Route' : 'New Camping';
        
        $title = $typeEn;
        $body = $site->name . ' - ' . ($site->description ? Str::limit($site->description, 100) : '');
        
        $data = [
            'type' => 'new_route_camping',
            'site_id' => (string)$site->id,
            'site_type' => $site->type,
            'site_name' => $site->name,
        ];

        return $this->sendToAllUsers($title, $body, $data);
    }

    /**
     * Send notification when user is accepted in a trip
     */
    public function notifyTripAccepted(User $user, $trip)
    {
        $title = 'Trip Accepted';
        $body = 'Your trip "' . $trip->trip_name . '" has been accepted!';
        
        $data = [
            'type' => 'trip_accepted',
            'trip_id' => (string)$trip->id,
            'trip_name' => $trip->trip_name,
        ];

        return $this->sendToUser($user, $title, $body, $data);
    }
}

