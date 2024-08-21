<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index() 
    {
        $notifications = Notification::all();

        if ($notifications->count() > 0) {
            return response()->json(['notifications' => $notifications]);
        } else {
            return response()->json([
                'notifications' => 'No notifications Found'
            ]);
        }
    }
    

    public function show($user_id)
{
    $notifications = Notification::where('user_id', $user_id)
                                ->where('status', 'approved')
                                ->get();

    return response()->json(['notifications' => $notifications]);
}

public function showAll($user_id)
{
    $notifications = Notification::where('user_id', $user_id)
                                ->get();

    return response()->json(['notifications' => $notifications]);
}
public function showSpecific($notification_id)
{
    $notifications = Notification::where('notification_id', $notification_id)
                                ->get();

    return response()->json(['notifications' => $notifications]);
}



public function update(Request $request, $id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->update($request->all());

        return response()->json([
            'message' => 'Notification updated successfully',
            'notification' => $notification
        ]);
    }
    
}
