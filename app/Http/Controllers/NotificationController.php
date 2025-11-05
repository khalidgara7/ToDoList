<?php

namespace App\Http\Controllers;

use App\Services\NotificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    protected NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:api');
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $unreadOnly = $request->query('unread_only', false);
        
        $result = $this->notificationService->getUserNotifications($user, (bool) $unreadOnly);

        return response()->json($result);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $result = $this->notificationService->markAsRead($id, $user);

        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $result = $this->notificationService->markAllAsRead($user);

        return response()->json($result);
    }
}