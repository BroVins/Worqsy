<?php
namespace App\Http\Controllers;

use App\Actions\SubmitTaskAction;
use App\Http\Requests\SubmitTaskRequest;
use App\Models\Task;
use App\Enums\TaskStatus;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;

class TaskSubmissionController extends Controller
{
    public function store(SubmitTaskRequest $request, Task $task, PermissionService $permissions, SubmitTaskAction $action): RedirectResponse
    {
        abort_unless($permissions->canWorkTask($request->user(),$task),403);
        abort_unless($task->status===TaskStatus::IN_PROGRESS,422,'Only In Progress work can be submitted.');
        $action->execute($task,$request->user(),$request->normalized());
        return back()->with('success','Pekerjaan dikirim untuk review.');
    }
}
