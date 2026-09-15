<?php
namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceAdminPermission;
use App\Models\WorkspaceMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->workspaceMemberships()->where('status','ACTIVE')->exists()) {
            return redirect()->route('home');
        }
        return view('onboarding.workspace');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->workspaceMemberships()->where('status','ACTIVE')->exists(),422,'User already belongs to a workspace.');

        $data=$request->validate([
            'name'=>['required','string','max:255'],
            'company_position'=>['nullable','string','max:120'],
        ]);

        $workspace=Workspace::create([
            'name'=>$data['name'],
            'slug'=>Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
            'status'=>'ACTIVE',
            'created_by'=>$request->user()->id,
        ]);

        // Workspace Creator is technical creation context; organization role is set separately.
        $membership=WorkspaceMembership::create([
            'workspace_id'=>$workspace->id,
            'user_id'=>$request->user()->id,
            'organization_role'=>'ADMIN',
            'company_position'=>$data['company_position'] ?: 'Workspace Administrator',
            'status'=>'ACTIVE',
            'joined_at'=>now(),
        ]);

        WorkspaceAdminPermission::create([
            'workspace_membership_id'=>$membership->id,
            'create_project'=>true,'manage_members'=>true,'manage_admins'=>true,'manage_guests'=>true,
            'manage_project_managers'=>true,'manage_permissions'=>true,'manage_lifecycle'=>true,
            'view_audit'=>true,'manage_billing'=>true,'manage_organization_settings'=>true,
        ]);

        $request->session()->put('workspace_id',$workspace->id);

        return redirect()->route('home')->with('success','Workspace created.');
    }
}
