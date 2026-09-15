<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\WorkspaceMembership;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectMembersPageController extends Controller
{
    public function __invoke(Request $request, Project $project, PermissionService $permissions): View
    {
        abort_unless($permissions->canViewProject($request->user(),$project),403);
        abort_if($permissions->isProjectGuest($request->user(),$project),403);
        $project->load(['memberships.user','memberships.position','memberships.phaseAccess']);
        $available=WorkspaceMembership::where('workspace_id',$project->workspace_id)->where('status','ACTIVE')->with('user')->get()->pluck('user')->filter();
        return view('projects.members',compact('project','available'));
    }
}
