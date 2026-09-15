<?php
namespace App\Http\Controllers;

use App\Actions\ApproveTaskAction;
use App\Actions\RequestRevisionAction;
use App\Enums\TaskStatus;
use App\Enums\OrganizationRole;
use App\Http\Requests\RequestRevisionRequest;
use App\Models\Task;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $user=$request->user();
        $workspace=$request->attributes->get('workspace');
        $permissions=app(PermissionService::class);
        abort_if($permissions->workspaceMembership($user,$workspace)?->organization_role===OrganizationRole::GUEST,403);
        $tasks=Task::where('status',TaskStatus::REVIEWING->value)
            ->where(function($q) use($user){
                $q->whereHas('reviewers',fn($r)=>$r->where('users.id',$user->id))
                  ->orWhereHas('approvers',fn($a)=>$a->where('users.id',$user->id));
            })
            ->with(['project','assignees'])->orderBy('due_date')->get();
        return view('reviews.index',compact('tasks'));
    }

    public function approve(Request $request, Task $task, PermissionService $permissions, ApproveTaskAction $action): RedirectResponse
    {
        abort_unless($permissions->canReviewTask($request->user(),$task),403);
        abort_unless($task->status === TaskStatus::REVIEWING,422);
        $action->execute($task,$request->user());
        return back()->with('success','Task disetujui.');
    }

    public function revision(RequestRevisionRequest $request, Task $task, PermissionService $permissions, RequestRevisionAction $action): RedirectResponse
    {
        abort_unless($permissions->canReviewTask($request->user(),$task),403);
        abort_unless($task->status === TaskStatus::REVIEWING,422);
        $action->execute($task,$request->user(),$request->validated());
        return back()->with('success','Revision berhasil dibuat.');
    }
}
