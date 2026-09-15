<?php
namespace App\Services;

use App\Models\AccessRecord;
use App\Models\User;
use App\Models\Workspace;

class AccessTracker
{
    public function touch(User $user, ?Workspace $workspace, string $resourceType, string $resourceId): void
    {
        $record = AccessRecord::firstOrNew([
            'user_id' => $user->id,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
        ]);

        if (! $record->exists) {
            $record->id = (string) \Illuminate\Support\Str::uuid();
            $record->workspace_id = $workspace?->id;
            $record->first_access_at = now();
            $record->access_count = 0;
        }

        $record->last_access_at = now();
        $record->access_count = ((int) $record->access_count) + 1;
        $record->save();
    }
}
