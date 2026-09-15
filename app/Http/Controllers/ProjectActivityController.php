<?php
namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\Project;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectActivityController extends Controller
{
    public function index(Request $request, Project $project, PermissionService $permissions): View
    {
        abort_unless($permissions->canViewProject($request->user(),$project),403);
        abort_if($permissions->isProjectGuest($request->user(),$project),403);
        $events=AuditEvent::where('workspace_id',$project->workspace_id)
            ->where(function($q) use($project){
                $q->where(fn($x)=>$x->where('resource_type','project')->where('resource_id',$project->id))
                  ->orWhere(fn($x)=>$x->where('resource_type','task')->whereIn('resource_id',$project->tasks()->pluck('id')));
            })->with('actor')->latest('created_at')->paginate(50);
        return view('projects.activity',compact('project','events'));
    }
}
