<?php
namespace App\Http\Controllers;

use App\Models\WorkspaceMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function switch(Request $request, string $workspace): RedirectResponse
    {
        $allowed = WorkspaceMembership::query()
            ->where('workspace_id',$workspace)
            ->where('user_id',$request->user()->id)
            ->where('status','ACTIVE')
            ->exists();

        abort_unless($allowed,403);

        $request->session()->put('workspace_id',$workspace);

        return redirect()->route('home');
    }
}
