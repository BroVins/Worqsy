<?php
namespace App\Actions;

use App\Enums\TaskStatus;
use App\Models\RevisionItem;
use App\Models\Task;
use App\Models\TaskRevision;
use App\Models\User;
use App\Services\AuditService;
use App\Notifications\WorqsyEventNotification;
use Illuminate\Support\Facades\DB;

class RequestRevisionAction
{
    public function __construct(private AuditService $audit) {}

    public function execute(Task $task, User $actor, array $data): TaskRevision
    {
        return DB::transaction(function () use ($task, $actor, $data) {
            $revision = TaskRevision::create([
                'task_id'=>$task->id,
                'revision_number'=>$task->revisions()->count()+1,
                'requested_by'=>$actor->id,
                'overall_deadline'=>$data['overall_deadline'] ?? null,
                'status'=>'OPEN',
                'request_note'=>$data['request_note'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                RevisionItem::create([
                    'revision_id'=>$revision->id,
                    'description'=>$item['description'],
                    'priority'=>$item['priority'] ?? 'MEDIUM',
                    'urgency'=>$item['urgency'] ?? 'NORMAL',
                    'deadline'=>$item['deadline'] ?? null,
                    'status'=>'OPEN',
                ]);
            }

            $task->update(['status'=>TaskStatus::REVISION]);
            $this->audit->record('task.revision_requested','task',$task->id,$actor,$task->project->workspace,null,['revision_number'=>$revision->revision_number]);
            foreach ($task->assignees()->get() as $assignee) {
                $assignee->notify(new WorqsyEventNotification('revision.requested','Revision requested',$task->title,'task',$task->id,route('tasks.show',$task)));
            }

            return $revision->fresh('items');
        });
    }
}
