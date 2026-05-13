<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\ClaimStatus;
use App\Enums\ModerationStatus;
use App\Enums\NotificationStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Claim;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $reportStatusCounts = Report::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('admin.dashboard.index', [
            'stats' => [
                'total_reports' => Report::count(),
                'pending_reports' => Report::where('moderation_status', ModerationStatus::Pending->value)->count(),
                'pending_claims' => Claim::where('status', ClaimStatus::Pending->value)->count(),
                'approved_reports' => Report::where('status', ReportStatus::Approved->value)->count(),
                'categories' => Category::count(),
                'users' => User::where('role', 'user')->count(),
            ],
            'reportStatusCounts' => $reportStatusCounts,
            'recentReports' => Report::with(['user', 'category'])->latest()->limit(6)->get(),
            'recentClaims' => Claim::with(['report', 'claimant'])->latest()->limit(6)->get(),
            'recentNotifications' => Notification::where('user_id', auth()->id())
                ->where('status', NotificationStatus::Unread->value)
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
