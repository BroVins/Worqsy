<?php
namespace App\Http\Controllers;

use App\Actions\CreateProjectAction;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkspaceMembership;
use App\Services\AccessTracker;
use App\Services\PermissionService;
use App\Services\ProjectProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request, ProjectProgressService $progress): View
    {
        $workspace=$request->attributes->get('workspace');
        $accessible=$request->user() ? app(PermissionService::class)->accessibleProjects($request->user(),$workspace) : collect();
        $projects=$accessible->sortByDesc('created_at')->values()
            ->map(fn($p)=>['project'=>$p,'progress'=>$progress->calculate($p),'health'=>$progress->health($p)->value]);
        return view('projects.index',compact('projects'));
    }

    public function create(Request $request, PermissionService $permissions): View
    {
        $workspace=$request->attributes->get('workspace');
        abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'create_project'),403);

        $users=WorkspaceMembership::where('workspace_id',$workspace->id)
            ->where('status','ACTIVE')->whereNotIn('organization_role',['OWNER','GUEST'])
            ->with('user')->get()->pluck('user')->filter();
        return view('projects.create',compact('users'));
    }

    public function store(StoreProjectRequest $request, PermissionService $permissions, CreateProjectAction $action): RedirectResponse
    {
        $workspace=$request->attributes->get('workspace');
        abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'create_project'),403);

        $project=$action->execute($request->user(),$workspace,$request->validated());
        return redirect()->route('projects.show',$project)->with('success','Project berhasil dibuat.');
    }

    public function show(Request $request, Project $project, PermissionService $permissions, ProjectProgressService $progress, AccessTracker $access): View
    {
        abort_unless($permissions->canViewProject($request->user(),$project),403);
        abort_unless($project->workspace_id === $request->attributes->get('workspace')->id,404);

        $access->touch($request->user(),$project->workspace,'project',$project->id);
        $isGuest=$permissions->isProjectGuest($request->user(),$project);
        $project->load(['currentPhaseCycle','phaseCycles']);
        if (! $isGuest) {
            $project->load(['memberships.user','memberships.position']);
        }
        $tasks=$project->tasks()
            ->when($isGuest,fn($q)=>$q->where('guest_visible',true))
            ->with(['assignees','reviewers'])->orderBy('due_date')->get();
        $data=[
            'progress'=>$progress->calculate($project),
            'health'=>$progress->health($project)->value,
            'breakdown'=>$progress->statusBreakdown($project),
        ];
        return view('projects.show',compact('project','tasks','data','isGuest'));
    }

    public function view(Request $request, Project $project, string $view, PermissionService $permissions, ProjectProgressService $progress): View
    {
        abort_unless($permissions->canViewProject($request->user(),$project),403);
        abort_unless(in_array($view,['list','board','calendar','timeline','grid'],true),404);

        $tasks=$project->tasks()
            ->when($permissions->isProjectGuest($request->user(),$project),fn($q)=>$q->where('guest_visible',true))
            ->with(['assignees','reviewers','approvers'])->orderBy('due_date')->get();
        return view('projects.views.'.$view,[
            'project'=>$project,
            'tasks'=>$tasks,
            'progress'=>$progress->calculate($project),
        ]);
    }
}
