<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(15);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, string $id = null): RedirectResponse
    {
        if ($id) {
            $notification = $request->user()
                ->notifications()
                ->where('id', $id)
                ->first();

            $notification?->markAsRead();
        } else {
            $request->user()->unreadNotifications->markAsRead();
        }

        return back()->with('status', 'Notifikasi ditandai sudah dibaca.');
    }
}
