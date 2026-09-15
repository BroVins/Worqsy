<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\ProjectMembership;
use App\Models\ProjectPhaseAccess;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkspaceMembership;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        abort_unless(config('services.google.client_id') && config('services.google.client_secret'), 503, 'Google OAuth belum dikonfigurasi pada .env.');
        return Socialite::driver('google')->redirect();
    }

    public function callback(AuditService $audit): RedirectResponse
    {
        $google = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $google->getEmail()],
            [
                'google_subject_id' => $google->getId(),
                'name' => $google->getName() ?: $google->getNickname() ?: $google->getEmail(),
                'avatar' => $google->getAvatar(),
                'status' => 'ACTIVE',
            ]
        );

        $this->activatePendingInvitations($user);

        Auth::login($user, true);
        request()->session()->regenerate();

        $audit->record('login.google','user',$user->id,$user,null,null,['email'=>$user->email],null,request());

        return $user->workspaceMemberships()->where('status','ACTIVE')->exists()
            ? redirect()->route('home')
            : redirect()->route('onboarding.workspace');
    }

    private function activatePendingInvitations(User $user): void
    {
        $invitations = Invitation::query()
            ->where('email', $user->email)
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at','>',now()))
            ->get();

        foreach ($invitations as $invitation) {
            WorkspaceMembership::firstOrCreate(
                ['workspace_id'=>$invitation->workspace_id,'user_id'=>$user->id],
                ['organization_role'=>'GUEST','company_position'=>$invitation->guest_type ?: 'External','status'=>'ACTIVE','joined_at'=>now()]
            );

            if ($invitation->project_id) {
                $projectMembership = ProjectMembership::firstOrCreate(
                    ['project_id'=>$invitation->project_id,'user_id'=>$user->id],
                    ['project_role'=>$invitation->project_role ?: 'GUEST','project_handle'=>'@Guest_'.preg_replace('/[^A-Za-z0-9]/','',$user->name),'permission_mode'=>$invitation->permission_mode,'status'=>'ACTIVE']
                );
                $project = Project::find($invitation->project_id);
                if ($project?->currentPhaseCycle) {
                    ProjectPhaseAccess::updateOrCreate(
                        ['phase_cycle_id'=>$project->currentPhaseCycle->id,'project_membership_id'=>$projectMembership->id],
                        ['access_status'=>'ACTIVE','activated_at'=>now(),'deactivated_at'=>null]
                    );
                }
            }

            $invitation->update(['accepted_at'=>now()]);
        }
    }
}
