<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Services\ProjectProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_approved_task_weight_counts_as_completed_progress(): void
    {
        $user=User::create(['name'=>'Admin','email'=>'admin@example.test','password'=>'password','status'=>'ACTIVE']);
        $workspace=Workspace::create(['name'=>'Test','slug'=>'test','status'=>'ACTIVE','created_by'=>$user->id]);
        $project=Project::create([
            'workspace_id'=>$workspace->id,'project_code'=>'TST001','name'=>'Test Project',
            'visibility'=>'PRIVATE','current_phase'=>'CREATE','status'=>'ACTIVE','created_by'=>$user->id,
        ]);

        Task::create(['project_id'=>$project->id,'task_code'=>'TST001-001','title'=>'Approved','status'=>'APPROVED','priority'=>'MEDIUM','weight'=>3,'created_by'=>$user->id]);
        Task::create(['project_id'=>$project->id,'task_code'=>'TST001-002','title'=>'Reviewing','status'=>'REVIEWING','priority'=>'MEDIUM','weight'=>1,'created_by'=>$user->id]);

        $progress=app(ProjectProgressService::class)->calculate($project);
        $this->assertSame(75.0,$progress);
    }
}
