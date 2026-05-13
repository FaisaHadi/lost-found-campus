<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Notifications\IndexNotificationRequest;
use App\Http\Resources\Api\V1\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function index(IndexNotificationRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Notification::class);

        $notifications = $this->notificationService->listForUser($request->user(), $request->validated());

        return $this->paginatedResponse(
            NotificationResource::collection($notifications),
            'Notifikasi berhasil diambil.'
        );
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $this->authorize('update', $notification);

        $notification = $this->notificationService->markAsRead($notification);

        return $this->successResponse(
            NotificationResource::make($notification),
            'Notifikasi ditandai sebagai dibaca.'
        );
    }
}
