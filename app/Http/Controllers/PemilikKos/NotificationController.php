<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display owner notifications
     */
    public function index()
    {
        $owner = Auth::user();
        $category = request('category', null);

        $query = Notification::where('user_id', $owner->id)
            ->with('relatedEntity')
            ->orderBy('created_at', 'desc');

        if ($category && $category !== 'semua') {
            $query->where('category', $category);
        }

        $notifications = $query->paginate(15);

        // Mark unread as read when viewing
        Notification::where('user_id', $owner->id)
            ->where('status', 'unread')
            ->update(['status' => 'read', 'read_at' => now()]);

        $categories = [
            'urgent' => 'Urgent & Approval',
            'finance' => 'Keuangan',
            'system' => 'Info Sistem',
            'info' => 'Informasi',
        ];

        return view('pemilik-kos.notifikasi', [
            'notifications' => $notifications,
            'categories' => $categories,
            'selectedCategory' => $category,
        ]);
    }

    /**
     * Archive notification
     */
    public function archive(Notification $notification)
    {
        // Check ownership
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['status' => 'archived']);

        return back()->with('success', 'Notifikasi berhasil diarsipkan');
    }
}
