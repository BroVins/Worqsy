<?php
namespace App\Actions;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\AuditService;
use App\Notifications\WorqsyEventNotification;
use Illuminate\Support\Facades\DB;

class CreateTaskAction
{
    public function __construct(private AuditService $audit) {}

    public function execute(Project $project, User $actor, array $data): Task
    {
        return DB::transaction(function () use ($project, $actor, $data) {
            $next = $project->tasks()->count() + 1;
            $task = Task::create([
                'project_id' => $project->id,
                'phase_cycle_id' => $project->currentPhaseCycle?->id,
                'task_code' => $project->project_code.'-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => TaskStatus::ASSIGNED,
                'priority' => $data['priority'] ?? 'MEDIUM',
                'weight' => $data['weight'] ?? 1,
                'guest_visible' => (bool) ($data['guest_visible'] ?? false),
                'estimate_minutes' => $data['estimate_minutes'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'created_by' => $actor->id,
            ]);

            if (! empty($data['assignee_ids'])) {
                $task->assignees()->sync($data['assignee_ids']);
                foreach ($task->assignees as $assignee) {
                    $assignee->notify(new WorqsyEventNotification('task.assigned','Task assigned',$task->title,'task',$task->id,route('tasks.show',$task)));
                }
            }
            if (! empty($data['reviewer_ids'])) $task->reviewers()->sync($data['reviewer_ids']);
            if (! empty($data['approver_ids'])) $task->approvers()->sync($data['approver_ids']);

            $this->audit->record('task.created','task',$task->id,$actor,$project->workspace,null,$task->toArray());

            return $task->fresh(['assignees','reviewers','approvers']);
        });
    }
}
