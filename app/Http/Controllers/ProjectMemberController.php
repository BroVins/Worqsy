<?php
namespace App\Http\Controllers;

use App\Enums\PermissionMode;
use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\ProjectMembership;
use App\Models\ProjectPhaseAccess;
use App\Models\ProjectPosition;
use App\Models\User;
use App\Services\PermissionService;
use App\Services\ProjectIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectMemberController extends Controller
{
    public function store(Request $request, Project $project, PermissionService $permissions, ProjectIdentityService $identity): RedirectResponse
    {
        abort_unless($permissions->canManageProject($request->user(),$project),403);

        $data=$request->validate([
            'user_id'=>['required','uuid','exists:users,id'],
            'project_role'=>['required',Rule::in(array_column(ProjectRole::cases(),'value'))],
            'position'=>['required','string','max:120'],
            'permission_mode'=>['required',Rule::in(array_column(PermissionMode::cases(),'value'))],
        ]);

        $user=User::findOrFail($data['user_id']);
        abort_unless(\App\Models\WorkspaceMembership::where('workspace_id',$project->workspace_id)->where('user_id',$user->id)->where('status','ACTIVE')->exists(),422,'User is not an active member of this workspace.');
        $slug=\Illuminate\Support\Str::slug($data['position']);
        $position=ProjectPosition::firstOrCreate(['project_id'=>$project->id,'slug'=>$slug],['name'=>$data['position']]);

        $membership=ProjectMembership::updateOrCreate(
            ['project_id'=>$project->id,'user_id'=>$user->id],
            [
                'project_role'=>$data['project_role'],
                'project_position_id'=>$position->id,
                'project_handle'=>$identity->make($user,$project,$data['position']),
                'permission_mode'=>$data['permission_mode'],
                'status'=>'ACTIVE',
            ]
        );

        if ($project->currentPhaseCycle) {
            ProjectPhaseAccess::updateOrCreate(
                ['phase_cycle_id'=>$project->currentPhaseCycle->id,'project_membership_id'=>$membership->id],
                ['access_status'=>'ACTIVE','activated_at'=>now(),'deactivated_at'=>null]
            );
        }

        return back()->with('success','Anggota project berhasil diperbarui.');
    }

    public function revoke(Request $request, Project $project, ProjectMembership $membership, PermissionService $permissions): RedirectResponse
    {
        abort_unless($permissions->canManageProject($request->user(),$project),403);
        abort_unless($membership->project_id===$project->id,404);

        $membership->update(['status'=>'REVOKED']);
        $membership->phaseAccess()->where('access_status','ACTIVE')->update([
            'access_status'=>'INACTIVE',
            'deactivated_at'=>now(),
        ]);

        return back()->with('success','Project access revoked. History remains preserved.');
    }

    public function reactivate(Request $request, Project $project, ProjectMembership $membership, PermissionService $permissions): RedirectResponse
    {
        abort_unless($permissions->canManageProject($request->user(),$project),403);
        abort_unless($membership->project_id===$project->id,404);

        $membership->update(['status'=>'ACTIVE']);
        if ($project->currentPhaseCycle) {
            ProjectPhaseAccess::updateOrCreate(
                ['phase_cycle_id'=>$project->currentPhaseCycle->id,'project_membership_id'=>$membership->id],
                ['access_status'=>'ACTIVE','activated_at'=>now(),'deactivated_at'=>null]
            );
        }

        return back()->with('success','Project membership reactivated for the current phase.');
    }
}
