<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class NotificationRepository
{
    public function paginateForUser(User $user, array $filters): LengthAwarePaginator
    {
        $query = Notification::query()
            ->with(['report.category', 'claim'])
            ->where('user_id', $user->id);

        $query->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_dir'] ?? 'desc';
        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->update([
            'status' => 'read',
            'read_at' => now(),
        ]);

        return $notification->refresh();
    }
}
