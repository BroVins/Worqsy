<?php
namespace App\Http\Controllers;

use App\Enums\OrganizationRole;
use App\Models\FileLink;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkspaceMembership;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request, PermissionService $permissions): View
    {
        $data=$request->validate(['q'=>['required','string','min:2','max:120']]);
        $q=trim($data['q']);
        $workspace=$request->attributes->get('workspace');
        $user=$request->user();
        $wm=$permissions->workspaceMembership($user,$workspace);
        $isGuest=$wm?->organization_role===OrganizationRole::GUEST;

        $accessible=$permissions->accessibleProjects($user,$workspace);
        $projectIds=$accessible->pluck('id');

        $projects=Project::whereIn('id',$projectIds)
            ->where(fn($x)=>$x->where('name','like',"%{$q}%")->orWhere('project_code','like',"%{$q}%"))
            ->limit(20)->get();

        $tasks=Task::whereIn('project_id',$projectIds)
            ->when($isGuest,fn($x)=>$x->where('guest_visible',true))
            ->where(fn($x)=>$x->where('title','like',"%{$q}%")->orWhere('task_code','like',"%{$q}%"))
            ->with('project')->limit(30)->get();

        $people=collect();
        if (! $isGuest) {
            $people=WorkspaceMembership::where('workspace_id',$workspace->id)
                ->where('status','ACTIVE')
                ->whereHas('user',fn($x)=>$x->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%"))
                ->with('user')->limit(20)->get();
        }

        $files=FileLink::query()
            ->where('resource_type','project')
            ->whereIn('resource_id',$projectIds)
            ->when($isGuest,fn($x)=>$x->where('guest_visible',true))
            ->whereHas('file',fn($x)=>$x->where('original_name','like',"%{$q}%"))
            ->with('file')->limit(20)->get();

        return view('search.index',compact('q','projects','tasks','people','files','isGuest'));
    }
}
