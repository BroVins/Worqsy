<?php
namespace App\Http\Controllers;

use App\Actions\CreateTaskAction;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\AccessTracker;
use App\Services\AuditService;
use App\Models\AuditEvent;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function create(Request $request, Project $project, PermissionService $permissions): View
    {
        abort_unless($permissions->canCreateTask($request->user(),$project),403);
        $members=$project->memberships()->where('status','ACTIVE')
            ->when($project->currentPhaseCycle, fn($q) => $q->whereHas('phaseAccess', fn($a) => $a
                ->where('phase_cycle_id',$project->currentPhaseCycle->id)->where('access_status','ACTIVE')))
            ->with(['user','position'])->get();
        return view('tasks.create',compact('project','members'));
    }

    public function store(StoreTaskRequest $request, Project $project, PermissionService $permissions, CreateTaskAction $action): RedirectResponse
    {
        abort_unless($permissions->canCreateTask($request->user(),$project),403);
        $task=$action->execute($project,$request->user(),$request->validated());
        return redirect()->route('tasks.show',$task)->with('success','Task berhasil dibuat.');
    }

    public function show(Request $request, Task $task, PermissionService $permissions, AccessTracker $access): View
    {
        abort_unless($permissions->canViewTask($request->user(),$task),403);
        $access->touch($request->user(),$task->project->workspace,'task',$task->id);

        $task->load(['project','assignees','reviewers','approvers','subtasks','submissions.submitter','revisions.items','comments.user']);
        $history=AuditEvent::where('resource_type','task')->where('resource_id',$task->id)->with('actor')->oldest('created_at')->get();
        return view('tasks.show',compact('task','history'));
    }

    public function start(Request $request, Task $task, PermissionService $permissions, AuditService $audit): RedirectResponse
    {
        abort_unless($permissions->canWorkTask($request->user(),$task),403);
        abort_unless($task->status === TaskStatus::ASSIGNED,422);
        $task->update(['status'=>TaskStatus::IN_PROGRESS]);
        $audit->record('task.started','task',$task->id,$request->user(),$task->project->workspace,['status'=>TaskStatus::ASSIGNED->value],['status'=>TaskStatus::IN_PROGRESS->value]);
        return back()->with('success','Task dimulai.');
    }

    public function comment(Request $request, Task $task, PermissionService $permissions, AuditService $audit): RedirectResponse
    {
        abort_unless($permissions->canViewTask($request->user(),$task) && $permissions->canComment($request->user(),$task->project),403);
        $data=$request->validate(['body'=>['required','string','max:5000']]);
        $task->comments()->create(['user_id'=>$request->user()->id,'body'=>$data['body']]);
        $audit->record('task.comment_added','task',$task->id,$request->user(),$task->project->workspace);
        return back()->with('success','Komentar ditambahkan.');
    }
}
