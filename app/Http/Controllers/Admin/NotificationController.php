<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications
     */
    public function index()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Mark as read
        Notification::where('user_id', $user->id)
            ->where('status', 'unread')
            ->update(['status' => 'read', 'read_at' => now()]);

        $categories = [
            'urgent' => 'Urgent & Approval',
            'finance' => 'Keuangan',
            'system' => 'Info Sistem',
            'info' => 'Informasi',
        ];

        return view('admin.notifikasi', [
            'notifications' => $notifications,
            'categories' => $categories,
        ]);
    }

    /**
     * Get notifications by category
     */
    public function byCategory($category)
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.notifikasi', [
            'notifications' => $notifications,
            'selectedCategory' => $category,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }

        return back();
    }

    /**
     * Archive notification
     */
    public function archive(Notification $notification)
    {
        if ($notification->user_id === Auth::id()) {
            $notification->update(['status' => 'archived']);
        }

        return back()->with('success', 'Notifikasi berhasil diarsipkan');
    }
}
