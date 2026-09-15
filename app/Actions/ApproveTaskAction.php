<?php
namespace App\Actions;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\AuditService;
use App\Notifications\WorqsyEventNotification;

class ApproveTaskAction
{
    public function __construct(private AuditService $audit) {}
    public function execute(Task $task, User $actor): void
    {
        $before = $task->status->value;
        $task->update(['status'=>TaskStatus::APPROVED,'approved_at'=>now()]);
        $this->audit->record('task.approved','task',$task->id,$actor,$task->project->workspace,['status'=>$before],['status'=>TaskStatus::APPROVED->value]);
        foreach ($task->assignees()->get() as $assignee) {
            $assignee->notify(new WorqsyEventNotification('task.approved','Work approved',$task->title,'task',$task->id,route('tasks.show',$task)));
        }
    }
}
