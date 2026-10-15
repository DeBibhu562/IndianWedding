<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\PaymentMilestone;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Support\Tenant;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function studio(): array
    {
        $org = Tenant::id();
        $key = 'dash.studio.v2.'.$org.'.'.now()->format('YmdHi');

        $overdueQuery = PaymentMilestone::query()->where(function ($q) {
            $q->where('status', PaymentStatus::Overdue)
                ->orWhere(function ($inner) {
                    $inner->whereIn('status', [PaymentStatus::Due, PaymentStatus::Pending])
                        ->whereDate('due_date', '<', now());
                });
        });

        $numbers = Cache::remember($key, 60, function () use ($overdueQuery) {
            $leads = Lead::query();
            $projects = Project::query();
            $totalLeads = (clone $leads)->count();
            $newLeads = (clone $leads)->where('status', LeadStatus::New)->count();
            $booked = (clone $leads)->where('status', LeadStatus::Booked)->count();

            return [
                'total_leads' => $totalLeads,
                'new_leads' => $newLeads,
                'conversion' => $totalLeads ? round(($booked / $totalLeads) * 100, 1) : 0,
                'active_projects' => (clone $projects)->whereNot('status', ProjectStatus::Completed)->count(),
                'upcoming_weddings' => (clone $projects)->whereNotNull('wedding_date')->where('wedding_date', '>=', now())->count(),
                'pending_payments' => PaymentMilestone::query()->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Due, PaymentStatus::PartiallyPaid])->sum('amount'),
                'overdue_payments' => (clone $overdueQuery)->count(),
                'open_tasks' => ProjectTask::query()->whereNot('status', 'completed')->count(),
                'revenue' => Payment::query()->where('status', PaymentStatus::Paid)->sum('amount'),
                'monthly_revenue' => Payment::query()->where('status', PaymentStatus::Paid)->whereMonth('paid_at', now()->month)->sum('amount'),
                'projects_by_status' => Project::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->all(),
            ];
        });

        $projectsByStatus = is_array($numbers['projects_by_status'] ?? null)
            ? $numbers['projects_by_status']
            : Project::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();

        return array_merge($numbers, [
            'projects_by_status' => $projectsByStatus,
            'projects_chart' => [
                'labels' => array_map(
                    fn (string $status) => ucwords(str_replace('_', ' ', $status)),
                    array_keys($projectsByStatus),
                ),
                'data' => array_values($projectsByStatus),
            ],
            'upcoming_list' => Project::query()->with('customer')->whereNotNull('wedding_date')->where('wedding_date', '>=', now()->toDateString())->orderBy('wedding_date')->limit(6)->get(),
            'new_leads_list' => Lead::query()->where('status', LeadStatus::New)->latest()->limit(5)->get(),
            'overdue_list' => (clone $overdueQuery)->with('project')->orderBy('due_date')->limit(5)->get(),
            'recent_activity' => ActivityLog::query()->with('actor')->where('organization_id', Tenant::id())->latest()->limit(8)->get(),
        ]);
    }
}
