<?php
namespace App\Http\Controllers;

use App\Actions\TransitionProjectPhaseAction;
use App\Enums\ProjectPhaseType;
use App\Models\Project;
use App\Services\PermissionService;
use App\Services\ProjectLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectPhaseController extends Controller
{
    public function transition(Request $request, Project $project, PermissionService $permissions, ProjectLifecycleService $lifecycle, TransitionProjectPhaseAction $action): RedirectResponse
    {
        abort_unless($permissions->canManageProject($request->user(),$project),403);

        $data=$request->validate([
            'next_phase'=>['required',Rule::in(array_column(ProjectPhaseType::cases(),'value'))],
            'active_membership_ids'=>['required','array','min:1'],
            'active_membership_ids.*'=>['uuid'],
        ]);

        $next=ProjectPhaseType::from($data['next_phase']);
        abort_unless($lifecycle->canTransition($project,$next),422,'Invalid project lifecycle transition.');
        $action->execute($project,$next,$request->user(),$data['active_membership_ids']??[]);
        return back()->with('success','Project lifecycle berhasil diperbarui.');
    }
}
