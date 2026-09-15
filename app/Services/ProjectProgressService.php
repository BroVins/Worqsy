<?php
namespace App\Services;

use App\Enums\ProjectHealthStatus;
use App\Enums\TaskStatus;
use App\Models\Project;

class ProjectProgressService
{
    public function calculate(Project $project): float
    {
        $tasks = $project->tasks()->get(['status','weight']);
        $total = $tasks->sum(fn ($task) => max(1, (int) $task->weight));
        if ($total === 0) return 0.0;

        $approved = $tasks
            ->filter(fn ($task) => $task->status === TaskStatus::APPROVED)
            ->sum(fn ($task) => max(1, (int) $task->weight));

        return round(($approved / $total) * 100, 2);
    }

    public function statusBreakdown(Project $project): array
    {
        $tasks = $project->tasks()->get();
        $totalWeight = max(1, $tasks->sum(fn ($task) => max(1, (int) $task->weight)));

        $result = [];
        foreach (TaskStatus::cases() as $status) {
            $weight = $tasks->filter(fn ($task) => $task->status === $status)
                ->sum(fn ($task) => max(1, (int) $task->weight));
            $result[$status->value] = round(($weight / $totalWeight) * 100, 2);
        }
        return $result;
    }

    public function health(Project $project): ProjectHealthStatus
    {
        $now = now();

        if ($project->tasks()->where('due_date', '<', $now)->where('status', '!=', TaskStatus::APPROVED->value)->exists()) {
            return ProjectHealthStatus::OVERDUE;
        }

        if ($project->tasks()->where('status', TaskStatus::REVISION->value)->count() >= config('worqsy.project_health.revision_backlog_threshold', 3)) {
            return ProjectHealthStatus::REVISION_REQUIRED;
        }

        if ($project->tasks()->where('status', TaskStatus::REVIEWING->value)->exists()) {
            return ProjectHealthStatus::WAITING_REVIEW;
        }

        $progress = $this->calculate($project);
        if ($project->target_completion && $project->target_completion->isBefore(now()->addDays(7)) && $progress < 80) {
            return ProjectHealthStatus::AT_RISK;
        }

        return ProjectHealthStatus::ON_TRACK;
    }
}
