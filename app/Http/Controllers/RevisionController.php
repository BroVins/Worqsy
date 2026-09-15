<?php
namespace App\Http\Controllers;

use App\Actions\ResubmitRevisionAction;
use App\Services\AuditService;
use App\Models\RevisionItem;
use App\Models\TaskRevision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RevisionController extends Controller
{
    public function updateItem(Request $request, RevisionItem $item, AuditService $audit): RedirectResponse
    {
        $revision=$item->revision()->with('task.assignees')->firstOrFail();
        abort_unless($revision->task->assignees->contains('id',$request->user()->id),403);

        $data=$request->validate(['status'=>['required',Rule::in(['OPEN','IN_PROGRESS','DONE'])]]);
        $before=$item->status->value;
        $item->update([
            'status'=>$data['status'],
            'completed_by'=>$data['status']==='DONE' ? $request->user()->id : null,
            'completed_at'=>$data['status']==='DONE' ? now() : null,
        ]);
        $audit->record('revision.item_updated','task',$revision->task_id,$request->user(),$revision->task->project->workspace,['item_id'=>$item->id,'status'=>$before],['item_id'=>$item->id,'status'=>$data['status']]);
        return back()->with('success','Revision item diperbarui.');
    }

    public function resubmit(Request $request, TaskRevision $revision, ResubmitRevisionAction $action): RedirectResponse
    {
        abort_unless($revision->task->assignees()->where('users.id',$request->user()->id)->exists(),403);
        $action->execute($revision,$request->user());
        return back()->with('success','Revision dikirim ulang untuk review.');
    }
}
