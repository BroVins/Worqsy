<?php
namespace App\Http\Controllers;

use App\Enums\OrganizationRole;
use App\Models\AccessRecord;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessLogController extends Controller
{
    public function __invoke(Request $request, PermissionService $permissions): View
    {
        $workspace=$request->attributes->get('workspace');
        $wm=$permissions->workspaceMembership($request->user(),$workspace);

        abort_unless(
            $wm?->organization_role===OrganizationRole::OWNER
            || $permissions->hasAdminPermission($request->user(),$workspace,'view_audit'),
            403
        );

        $records=AccessRecord::where('workspace_id',$workspace->id)
            ->with('user')
            ->latest('last_access_at')
            ->paginate(50);

        return view('audit.access',compact('records'));
    }
}
