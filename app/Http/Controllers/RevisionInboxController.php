<?php
namespace App\Http\Controllers;

use App\Models\TaskRevision;
use App\Enums\OrganizationRole;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevisionInboxController extends Controller
{
    public function __invoke(Request $request, PermissionService $permissions): View
    {
        $user=$request->user();
        $workspace=$request->attributes->get('workspace');
        abort_if($permissions->workspaceMembership($user,$workspace)?->organization_role===OrganizationRole::GUEST,403);
        $revisions=TaskRevision::whereHas('task.assignees',fn($q)=>$q->where('users.id',$user->id))
            ->where('status','OPEN')->with(['task.project','items'])->latest()->get();
        return view('revisions.index',compact('revisions'));
    }
}
