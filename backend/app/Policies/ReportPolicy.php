<?php

namespace App\Policies;

use App\Enums\ModerationStatus;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin()
            || $report->user_id === $user->id
            || $report->moderation_status === ModerationStatus::Approved;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Report $report): bool
    {
        return $user->isAdmin() || $report->user_id === $user->id;
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->isAdmin() || $report->user_id === $user->id;
    }

    public function approve(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }
}
