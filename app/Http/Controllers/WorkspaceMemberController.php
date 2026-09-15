<?php
namespace App\Http\Controllers;

use App\Enums\OrganizationRole;
use App\Models\WorkspaceAdminPermission;
use App\Models\WorkspaceMembership;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkspaceMemberController extends Controller
{
    private array $adminFields=[
        'create_project','manage_members','manage_admins','manage_guests',
        'manage_project_managers','manage_permissions','manage_lifecycle',
        'view_audit','manage_billing','manage_organization_settings',
    ];

    private function authorizeManage(Request $request, WorkspaceMembership $membership, PermissionService $permissions): void
    {
        $workspace=$request->attributes->get('workspace');
        abort_unless($membership->workspace_id===$workspace->id,404);
        abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'manage_members'),403);

        if ($membership->organization_role===OrganizationRole::ADMIN) {
            abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'manage_admins'),403);
        }
    }

    public function edit(Request $request, WorkspaceMembership $membership, PermissionService $permissions): View
    {
        $this->authorizeManage($request,$membership,$permissions);
        $membership->load(['user','adminPermission']);
        return view('people.edit',compact('membership'));
    }

    public function update(Request $request, WorkspaceMembership $membership, PermissionService $permissions): RedirectResponse
    {
        $this->authorizeManage($request,$membership,$permissions);
        $workspace=$request->attributes->get('workspace');

        $data=$request->validate([
            'organization_role'=>['required',Rule::in(['OWNER','ADMIN','MEMBER','GUEST'])],
            'company_position'=>['nullable','string','max:120'],
            'scope'=>['nullable','string','max:120'],
            'status'=>['required',Rule::in(['ACTIVE','INACTIVE','REVOKED'])],
        ]);

        if ($data['organization_role']==='ADMIN') {
            abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'manage_admins'),403);
        }

        abort_if($membership->user_id===$request->user()->id && $data['status']!=='ACTIVE',422,'You cannot deactivate your own current workspace membership.');

        $membership->update($data);

        if ($data['organization_role']==='ADMIN') {
            $admin=WorkspaceAdminPermission::firstOrCreate(['workspace_membership_id'=>$membership->id]);
            $updates=[];
            foreach($this->adminFields as $field) $updates[$field]=$request->boolean($field);
            $admin->update($updates);
        }

        return redirect()->route('people.index')->with('success','Workspace membership updated.');
    }

    public function revoke(Request $request, WorkspaceMembership $membership, PermissionService $permissions): RedirectResponse
    {
        $this->authorizeManage($request,$membership,$permissions);
        abort_if($membership->user_id===$request->user()->id,422,'You cannot revoke your own current workspace membership.');

        $membership->update(['status'=>'REVOKED']);
        $membership->user->projectMemberships()
            ->whereHas('project',fn($q)=>$q->where('workspace_id',$membership->workspace_id))
            ->update(['status'=>'REVOKED']);

        return back()->with('success','Workspace access revoked. Historical identity and audit records are preserved.');
    }

    public function reactivate(Request $request, WorkspaceMembership $membership, PermissionService $permissions): RedirectResponse
    {
        $this->authorizeManage($request,$membership,$permissions);
        $membership->update(['status'=>'ACTIVE']);
        return back()->with('success','Workspace membership reactivated. Project phase access may still require explicit reactivation.');
    }
}
