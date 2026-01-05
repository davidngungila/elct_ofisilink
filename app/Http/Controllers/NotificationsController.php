<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationsController extends Controller
{
    public function unread(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success'=>false]);
        }

        // Get current date start and end
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Only get notifications for current date (exclude login notifications)
        $items = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('message', 'not like', '%successfully logged into%')
            ->latest()
            ->limit(5)
            ->get(['id','message','link','created_at']);

        // Get total unread count for current day only (exclude login notifications)
        $totalUnreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('message', 'not like', '%successfully logged into%')
            ->count();

        // Get session key for tracking shown notifications this login
        $sessionKey = 'notifications_shown_' . $userId;
        $shownIds = session($sessionKey, []);

        // Filter out notifications already shown in this session
        $newNotifications = $items->filter(function($n) use ($shownIds) {
            return !in_array($n->id, $shownIds);
        });

        return response()->json([
            'success' => true,
            'count' => $totalUnreadCount, // Return total unread count for current day
            'notifications' => $newNotifications->map(function($n){
                return [
                    'id' => $n->id,
                    'message' => $n->message,
                    'link' => $n->link,
                    'time' => optional($n->created_at)->diffForHumans(),
                ];
            }),
        ]);
    }

    public function dropdown(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success'=>false]);
        }
        
        // Get current date start and end
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        
        // Get only notifications for current date (exclude login notifications)
        $items = Notification::where('user_id', $userId)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('message', 'not like', '%successfully logged into%')
            ->latest()
            ->limit(5)
            ->get(['id','message','link','is_read','created_at']);
        
        // Get total unread count for current day only (exclude login notifications)
        $totalUnreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('message', 'not like', '%successfully logged into%')
            ->count();
        
        return response()->json([
            'success'=> true,
            'count_unread' => $totalUnreadCount, // Total unread count for current day only
            'items' => $items->map(function($n){
                return [
                    'id'=>$n->id,
                    'message'=>$n->message,
                    'link'=>$n->link,
                    'is_read'=>$n->is_read,
                    'time'=> optional($n->created_at)->diffForHumans(),
                ];
            })
        ]);
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        $notification->update(['is_read'=>true]);
        
        // Mark as shown in session
        $userId = Auth::id();
        $sessionKey = 'notifications_shown_' . $userId;
        $shownIds = session($sessionKey, []);
        if (!in_array($notification->id, $shownIds)) {
            $shownIds[] = $notification->id;
            session([$sessionKey => $shownIds]);
        }
        
        return response()->json(['success'=>true]);
    }
    
    public function markShown(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success'=>false]);
        }
        
        $notificationId = $request->input('notification_id');
        if (!$notificationId) {
            return response()->json(['success'=>false, 'message' => 'Notification ID required']);
        }
        
        // Verify notification belongs to user
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
        
        if (!$notification) {
            return response()->json(['success'=>false, 'message' => 'Notification not found']);
        }
        
        // Mark as shown in session (for popup tracking)
        $sessionKey = 'notifications_shown_' . $userId;
        $shownIds = session($sessionKey, []);
        if (!in_array($notificationId, $shownIds)) {
            $shownIds[] = $notificationId;
            session([$sessionKey => $shownIds]);
        }
        
        return response()->json(['success'=>true]);
    }
    
    public function clearLoginNotification(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success'=>false]);
        }
        
        // Clear login notification from session
        session()->forget('login_notification_message');
        session()->forget('login_notification_time');
        
        return response()->json(['success'=>true]);
    }
}








