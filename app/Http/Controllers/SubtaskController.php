<?php
namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task, PermissionService $permissions): RedirectResponse
    {
        abort_unless(
            $permissions->canWorkTask($request->user(),$task)
            || $permissions->canCreateTask($request->user(),$task->project),
            403
        );

        $data=$request->validate(['title'=>['required','string','max:255']]);
        $task->subtasks()->create(['title'=>$data['title'],'is_completed'=>false]);
        return back()->with('success','Subtask added.');
    }

    public function toggle(Request $request, Subtask $subtask, PermissionService $permissions): RedirectResponse
    {
        $task=$subtask->task;
        abort_unless($permissions->canWorkTask($request->user(),$task),403);

        $completed=!$subtask->is_completed;
        $subtask->update([
            'is_completed'=>$completed,
            'completed_at'=>$completed?now():null,
        ]);

        return back();
    }
}
