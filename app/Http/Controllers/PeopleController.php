<?php
namespace App\Http\Controllers;

use App\Enums\OrganizationRole;
use App\Models\WorkspaceMembership;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeopleController extends Controller
{
    public function index(Request $request): View
    {
        $workspace=$request->attributes->get('workspace');
        $wm=$request->user()->workspaceMemberships()->where('workspace_id',$workspace->id)->firstOrFail();
        abort_unless(in_array($wm->organization_role,[OrganizationRole::OWNER,OrganizationRole::ADMIN],true),403);

        $people=WorkspaceMembership::where('workspace_id',$workspace->id)->with(['user','adminPermission'])->orderBy('organization_role')->get();
        return view('people.index',compact('people'));
    }
}
