<?php
namespace App\Http\Controllers;

use App\Enums\OrganizationRole;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Services\ProjectProgressService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request, ProjectProgressService $progress, PermissionService $permissions): View
    {
        $workspace = $request->attributes->get('workspace');
        $membership = $request->user()->workspaceMemberships()
            ->where('workspace_id',$workspace->id)->first();

        if ($membership?->organization_role === OrganizationRole::OWNER) {
            $projects = Project::where('workspace_id',$workspace->id)->get();
            $cards = $projects->map(fn($project)=>[
                'project'=>$project,
                'progress'=>$progress->calculate($project),
                'health'=>$progress->health($project)->value,
            ]);
            return view('dashboard.owner', compact('workspace','cards','projects'));
        }

        if ($membership?->organization_role === OrganizationRole::ADMIN) {
            $projects = Project::where('workspace_id',$workspace->id)->get();
            $cards = $projects->map(fn($project)=>[
                'project'=>$project,
                'progress'=>$progress->calculate($project),
                'health'=>$progress->health($project)->value,
            ]);
            $reviewBacklog=Task::whereIn('project_id',$projects->pluck('id'))->where('status',TaskStatus::REVIEWING->value)->count();
            $revisionBacklog=Task::whereIn('project_id',$projects->pluck('id'))->where('status',TaskStatus::REVISION->value)->count();
            $guestCount=$workspace->memberships()->where('organization_role','GUEST')->where('status','ACTIVE')->count();
            return view('dashboard.admin',compact('workspace','cards','projects','reviewBacklog','revisionBacklog','guestCount'));
        }

        if ($membership?->organization_role === OrganizationRole::GUEST) {
            $user=$request->user();
            $projects=$permissions->accessibleProjects($user,$workspace)
                ->map(fn($project)=>['project'=>$project,'progress'=>$progress->calculate($project),'health'=>$progress->health($project)->value]);
            return view('dashboard.guest',compact('workspace','projects'));
        }

        $user=$request->user();
        $projectIds=$permissions->accessibleProjects($user,$workspace)->pluck('id');
        $tasks=Task::query()
            ->whereIn('project_id',$projectIds)
            ->whereHas('assignees',fn($q)=>$q->where('users.id',$user->id))
            ->with('project')
            ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->limit(30)->get();

        $reviews=Task::query()
            ->whereIn('project_id',$projectIds)
            ->where('status',TaskStatus::REVIEWING->value)
            ->where(function($q) use($user){
                $q->whereHas('reviewers',fn($r)=>$r->where('users.id',$user->id))
                  ->orWhereHas('approvers',fn($a)=>$a->where('users.id',$user->id));
            })
            ->with('project')->limit(10)->get();

        $projects=Project::whereIn('id',$projectIds)->get()
            ->map(fn($project)=>['project'=>$project,'progress'=>$progress->calculate($project),'health'=>$progress->health($project)->value]);

        return view('dashboard.my-work',compact('workspace','tasks','reviews','projects'));
    }
}
