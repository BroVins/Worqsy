<?php
namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request, PermissionService $permissions): View
    {
        $workspace=$request->attributes->get('workspace');
        $wm=$permissions->workspaceMembership($request->user(),$workspace);
        abort_unless($wm?->organization_role?->value==='OWNER' || $permissions->hasAdminPermission($request->user(),$workspace,'view_audit'),403);

        $events=AuditEvent::where('workspace_id',$workspace->id)->with('actor')->latest('created_at')->paginate(50);
        return view('audit.index',compact('events'));
    }
}
