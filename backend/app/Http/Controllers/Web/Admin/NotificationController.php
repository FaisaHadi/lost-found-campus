<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\NotificationFilterRequest;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function index(NotificationFilterRequest $request): View
    {
        $this->authorize('viewAny', Notification::class);

        return view('admin.notifications.index', [
            'notifications' => $this->notificationService->listForUser($request->user(), $request->validated()),
            'filters' => $request->validated(),
        ]);
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $this->authorize('update', $notification);

        $this->notificationService->markAsRead($notification);

        return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }
}
