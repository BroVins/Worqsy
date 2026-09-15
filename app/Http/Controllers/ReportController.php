<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Enums\OrganizationRole;
use App\Services\ProjectProgressService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, ProjectProgressService $progress): View
    {
        $workspace=$request->attributes->get('workspace');
        $wm=$request->user()->workspaceMemberships()->where('workspace_id',$workspace->id)->firstOrFail();
        abort_unless(in_array($wm->organization_role,[OrganizationRole::OWNER,OrganizationRole::ADMIN],true),403);
        $projects=Project::where('workspace_id',$workspace->id)->get();
        $rows=$projects->map(fn($p)=>[
            'project'=>$p,
            'progress'=>$progress->calculate($p),
            'health'=>$progress->health($p)->value,
            'overdue'=>$p->tasks()->where('due_date','<',now())->where('status','!=','APPROVED')->count(),
            'reviewing'=>$p->tasks()->where('status','REVIEWING')->count(),
            'revision'=>$p->tasks()->where('status','REVISION')->count(),
        ]);
        return view('reports.index',compact('rows'));
    }
}
