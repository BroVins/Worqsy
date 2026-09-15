<?php
namespace App\Actions;

use App\Enums\TaskStatus;
use App\Models\TaskRevision;
use App\Models\User;
use App\Services\AuditService;

class ResubmitRevisionAction
{
    public function __construct(private AuditService $audit) {}

    public function execute(TaskRevision $revision, User $actor): void
    {
        abort_if($revision->items()->where('status','!=','DONE')->exists(), 422, 'All revision items must be completed before resubmission.');

        $revision->update(['status'=>'RESUBMITTED','resubmitted_at'=>now()]);
        $revision->task->update(['status'=>TaskStatus::RESUBMITTED]);
        $this->audit->record('task.revision_resubmitted','task',$revision->task_id,$actor,$revision->task->project->workspace,['status'=>TaskStatus::REVISION->value],['status'=>TaskStatus::RESUBMITTED->value,'revision_number'=>$revision->revision_number]);

        $revision->task->update(['status'=>TaskStatus::REVIEWING]);
        $this->audit->record('task.review_restarted','task',$revision->task_id,$actor,$revision->task->project->workspace,['status'=>TaskStatus::RESUBMITTED->value],['status'=>TaskStatus::REVIEWING->value]);
    }
}
