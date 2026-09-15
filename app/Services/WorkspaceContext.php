<?php
namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMembership;
use Illuminate\Http\Request;

class WorkspaceContext
{
    public function current(Request $request): ?Workspace
    {
        $user = $request->user();
        if (! $user) return null;

        $workspaceId = $request->session()->get('workspace_id');

        $membership = WorkspaceMembership::query()
            ->where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->when($workspaceId, fn ($q) => $q->where('workspace_id', $workspaceId))
            ->with('workspace')
            ->first();

        if (! $membership) {
            $membership = WorkspaceMembership::query()
                ->where('user_id', $user->id)
                ->where('status', 'ACTIVE')
                ->with('workspace')
                ->first();
        }

        if ($membership) {
            $request->session()->put('workspace_id', $membership->workspace_id);
            return $membership->workspace;
        }

        return null;
    }

    public function membership(User $user, Workspace $workspace): ?WorkspaceMembership
    {
        return WorkspaceMembership::query()
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('adminPermission')
            ->first();
    }
}
