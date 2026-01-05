<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class SendLoginNotification
{
    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            $user = $event->user;
            
            // Check if login notification was already sent for this session
            // This prevents showing the notification on page refresh
            $sessionKey = 'login_notification_sent_' . $user->id;
            if (session()->has($sessionKey)) {
                // Already sent for this login session, skip
                return;
            }
            
            // Get login details
            $ipAddress = request()->ip();
            $userAgent = request()->userAgent();
            $loginTime = now()->format('Y-m-d H:i:s');
            
            // Build login notification message
            $message = "You have successfully logged into your OfisiLink account.";
            
            // Store login notification in session only (NO database entry)
            // Frontend will read from session and show toast once
            session([
                $sessionKey => true,
                'login_notification_message' => $message,
                'login_notification_time' => now()->toIso8601String()
            ]);
            
            Log::info('Login notification prepared (session only, no DB entry)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $ipAddress,
            ]);
        } catch (\Exception $e) {
            // Don't fail login if notification fails
            Log::warning('Failed to prepare login notification', [
                'user_id' => $event->user->id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }
}




