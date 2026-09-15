<?php
namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Project;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    public function store(Request $request, PermissionService $permissions): RedirectResponse
    {
        $workspace=$request->attributes->get('workspace');
        abort_unless($permissions->hasAdminPermission($request->user(),$workspace,'manage_guests'),403);

        $data=$request->validate([
            'email'=>['required','email'],
            'project_id'=>['nullable','uuid',Rule::exists('projects','id')->where(fn($q)=>$q->where('workspace_id',$workspace->id))],
            'guest_type'=>['required','string','max:80'],
            'permission_mode'=>['required',Rule::in(['READ_ONLY','COMMENT_ONLY','RESTRICTED'])],
        ]);

        $token=Str::random(64);
        Invitation::create([
            'workspace_id'=>$workspace->id,
            'project_id'=>$data['project_id']??null,
            'email'=>$data['email'],
            'guest_type'=>$data['guest_type'],
            'project_role'=>'GUEST',
            'permission_mode'=>$data['permission_mode'],
            'token_hash'=>hash('sha256',$token),
            'expires_at'=>now()->addDays(7),
            'invited_by'=>$request->user()->id,
        ]);

        return back()->with('success','Guest invitation dibuat. Google login dengan email tersebut akan mengaktifkan akses yang diberikan.');
    }
}
