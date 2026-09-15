<?php
namespace App\Services;

use App\Enums\ProjectPhaseType;
use App\Models\Project;

class ProjectLifecycleService
{
    public function allowed(Project $project): array
    {
        return match($project->current_phase) {
            ProjectPhaseType::CREATE => [ProjectPhaseType::MAINTENANCE],
            ProjectPhaseType::MAINTENANCE => [ProjectPhaseType::DEVELOPMENT, ProjectPhaseType::CLOSED],
            ProjectPhaseType::DEVELOPMENT => [ProjectPhaseType::MAINTENANCE, ProjectPhaseType::CLOSED],
            ProjectPhaseType::CLOSED => [],
        };
    }

    public function canTransition(Project $project, ProjectPhaseType $next): bool
    {
        return in_array($next, $this->allowed($project), true);
    }
}
