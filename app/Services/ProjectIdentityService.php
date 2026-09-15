<?php
namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Str;

class ProjectIdentityService
{
    public function make(User $user, Project $project, string $position): string
    {
        $positionPart = Str::of($position)->ascii()->replaceMatches('/[^A-Za-z0-9]/', '')->limit(28, '');
        $namePart = Str::of($user->name)->ascii()->replaceMatches('/[^A-Za-z0-9 ]/', '')->explode(' ')->first() ?: 'User';
        return '@'.$positionPart.'_'.$namePart.'_'.$project->project_code;
    }
}
