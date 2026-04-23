<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        // جلب الإشعارات غير المقروءة
        $notifications = auth()->user()->unreadNotifications;
        
        return response()->json($notifications);
    }

   public function markAsRead($id): JsonResponse
{
   
    $notification = auth()->user()->notifications()->where('id', $id)->first();

    if (!$notification) {
        return response()->json([
            'message' => 'الإشعار غير موجود أو لا تملك صلاحية الوصول إليه',
            'requested_id' => $id
        ], 404);
    }

    $notification->markAsRead();

    return response()->json(['message' => 'Notification marked as read']);
}
}