<?php
namespace App\Http\Controllers;

use App\Enums\OrganizationRole;
use App\Models\User;
use App\Models\WorkspaceAdminPermission;
use App\Models\WorkspaceMembership;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternalMemberController extends Controller
{
    public function store(Request $request, PermissionService $permissions): RedirectResponse
    {
        $workspace=$request->attributes->get('workspace');
        abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'manage_members'),403);

        $data=$request->validate([
            'name'=>['required','string','max:255'],
            'email'=>['required','email'],
            'organization_role'=>['required',Rule::in(['OWNER','ADMIN','MEMBER'])],
            'company_position'=>['nullable','string','max:120'],
            'scope'=>['nullable','string','max:120'],
        ]);

        if ($data['organization_role']==='ADMIN') {
            abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'manage_admins'),403);
        }

        $user=User::firstOrCreate(
            ['email'=>$data['email']],
            ['name'=>$data['name'],'status'=>'ACTIVE']
        );
        if ($user->name !== $data['name'] && ! $user->google_subject_id) $user->update(['name'=>$data['name']]);

        $membership=WorkspaceMembership::updateOrCreate(
            ['workspace_id'=>$workspace->id,'user_id'=>$user->id],
            ['organization_role'=>$data['organization_role'],'company_position'=>$data['company_position']??null,'scope'=>$data['scope']??null,'status'=>'ACTIVE','joined_at'=>now()]
        );

        if ($data['organization_role']==='ADMIN') {
            WorkspaceAdminPermission::firstOrCreate(
                ['workspace_membership_id'=>$membership->id],
                ['create_project'=>true,'manage_members'=>true,'manage_guests'=>true,'manage_project_managers'=>true,'manage_lifecycle'=>true,'view_audit'=>true]
            );
        }

        return back()->with('success','Internal member pre-registered. Google login with this email will use the existing membership.');
    }
}
