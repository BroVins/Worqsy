<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Enums\OrganizationRole;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request, PermissionService $permissions): View
    {
        $workspace=$request->attributes->get('workspace');
        $user=$request->user();
        $wm=$permissions->workspaceMembership($user,$workspace);
        abort_if($wm?->organization_role===OrganizationRole::GUEST,403);
        $projectIds=$permissions->accessibleProjects($user,$workspace)->pluck('id');
        $tasks=Task::whereIn('project_id',$projectIds)->whereNotNull('due_date')->with('project')->orderBy('due_date')->get();
        return view('calendar.index',compact('tasks'));
    }
}
