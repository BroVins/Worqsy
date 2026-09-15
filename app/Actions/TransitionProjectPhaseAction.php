<?php
namespace App\Actions;

use App\Enums\ProjectPhaseType;
use App\Models\Project;
use App\Models\ProjectPhaseAccess;
use App\Models\ProjectPhaseCycle;
use App\Models\User;
use App\Services\AuditService;
use App\Notifications\WorqsyEventNotification;
use Illuminate\Support\Facades\DB;

class TransitionProjectPhaseAction
{
    public function __construct(private AuditService $audit) {}

    public function execute(Project $project, ProjectPhaseType $nextPhase, User $actor, array $activeMembershipIds = []): ProjectPhaseCycle
    {
        return DB::transaction(function () use ($project, $nextPhase, $actor, $activeMembershipIds) {
            $current = $project->currentPhaseCycle()->first();
            $before = ['current_phase' => $project->current_phase?->value ?? $project->current_phase];

            if ($current) {
                $current->update(['status'=>'COMPLETED','completed_at'=>now()]);
            }

            $cycleNumber = ProjectPhaseCycle::query()
                ->where('project_id', $project->id)
                ->where('phase_type', $nextPhase->value)
                ->max('cycle_number') ?? 0;

            $newCycle = ProjectPhaseCycle::create([
                'project_id' => $project->id,
                'phase_type' => $nextPhase,
                'cycle_number' => $cycleNumber + 1,
                'title' => $nextPhase->value.' #'.($cycleNumber + 1),
                'started_at' => now(),
                'status' => $nextPhase === ProjectPhaseType::CLOSED ? 'COMPLETED' : 'ACTIVE',
                'completed_at' => $nextPhase === ProjectPhaseType::CLOSED ? now() : null,
            ]);

            $project->update([
                'current_phase' => $nextPhase,
                'status' => $nextPhase === ProjectPhaseType::CLOSED ? 'CLOSED' : 'ACTIVE',
            ]);

            $activeMembershipIds = $activeMembershipIds ?: [];
            foreach ($project->memberships()->where('status','ACTIVE')->get() as $membership) {
                ProjectPhaseAccess::create([
                    'phase_cycle_id' => $newCycle->id,
                    'project_membership_id' => $membership->id,
                    'access_status' => in_array($membership->id, $activeMembershipIds, true) ? 'ACTIVE' : 'INACTIVE',
                    'activated_at' => in_array($membership->id, $activeMembershipIds, true) ? now() : null,
                    'deactivated_at' => in_array($membership->id, $activeMembershipIds, true) ? null : now(),
                ]);
            }

            $this->audit->record('project.phase_changed','project',$project->id,$actor,$project->workspace,$before,['current_phase'=>$nextPhase->value]);

            foreach ($project->memberships()->where('status','ACTIVE')->with('user')->get() as $membership) {
                $membership->user?->notify(new WorqsyEventNotification(
                    'project.phase_changed',
                    'Project phase changed',
                    $project->project_code.' is now '.$nextPhase->value,
                    'project',
                    $project->id,
                    route('projects.show',$project)
                ));
            }

            return $newCycle;
        });
    }
}
