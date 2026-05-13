<?php

namespace App\Services;

use App\Enums\NotificationStatus;
use App\Models\Claim;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository
    ) {}

    public function listForUser(User $user, array $filters): LengthAwarePaginator
    {
        return $this->notificationRepository->paginateForUser($user, $filters);
    }

    public function markAsRead(Notification $notification): Notification
    {
        return $this->notificationRepository->markAsRead($notification);
    }

    public function createForUser(
        User|int $user,
        string $title,
        string $message,
        ?Report $report = null,
        ?Claim $claim = null
    ): Notification {
        return $this->notificationRepository->create([
            'user_id' => $user instanceof User ? $user->id : $user,
            'report_id' => $report?->id,
            'claim_id' => $claim?->id,
            'title' => $title,
            'message' => $message,
            'status' => NotificationStatus::Unread->value,
        ]);
    }
}
