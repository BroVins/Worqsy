<?php
namespace App\Actions;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Services\AuditService;
use App\Notifications\WorqsyEventNotification;
use Illuminate\Support\Facades\DB;

class SubmitTaskAction
{
    public function __construct(private AuditService $audit) {}

    public function execute(Task $task, User $actor, array $data): TaskSubmission
    {
        return DB::transaction(function () use ($task, $actor, $data) {
            $submission = TaskSubmission::create([
                'task_id' => $task->id,
                'submitted_by' => $actor->id,
                'submission_number' => $task->submissions()->count() + 1,
                'work_summary' => $data['work_summary'],
                'deliverables' => $data['deliverables'] ?? [],
                'links' => $data['links'] ?? [],
                'completion_notes' => $data['completion_notes'] ?? null,
                'submitted_at' => now(),
            ]);

            $task->update(['status' => TaskStatus::DONE_SUBMITTED]);
            $this->audit->record('task.submitted','task',$task->id,$actor,$task->project->workspace,['status'=>TaskStatus::IN_PROGRESS->value],['status'=>TaskStatus::DONE_SUBMITTED->value]);

            $task->update(['status' => TaskStatus::REVIEWING]);
            $this->audit->record('task.review_started','task',$task->id,$actor,$task->project->workspace,['status'=>TaskStatus::DONE_SUBMITTED->value],['status'=>TaskStatus::REVIEWING->value]);

            foreach ($task->reviewers()->get()->merge($task->approvers()->get())->unique('id') as $reviewer) {
                $reviewer->notify(new WorqsyEventNotification('review.required','Review required',$task->title,'task',$task->id,route('tasks.show',$task)));
            }

            return $submission;
        });
    }
}
