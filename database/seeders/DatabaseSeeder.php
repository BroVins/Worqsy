<?php
namespace Database\Seeders;

use App\Enums\PermissionMode;
use App\Enums\ProjectPhaseType;
use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\ProjectMembership;
use App\Models\ProjectPhaseAccess;
use App\Models\ProjectPhaseCycle;
use App\Models\ProjectPosition;
use App\Models\RevisionItem;
use App\Models\Task;
use App\Models\TaskRevision;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceAdminPermission;
use App\Models\WorkspaceMembership;
use App\Services\ProjectIdentityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            ['key'=>'budi','name'=>'Budi Santoso','email'=>'budi@worqsy.local'],
            ['key'=>'raka','name'=>'Raka Pratama','email'=>'raka@worqsy.local'],
            ['key'=>'abdul','name'=>'Abdul Rahman','email'=>'abdul@worqsy.local'],
            ['key'=>'arif','name'=>'Arif Setiawan','email'=>'arif@worqsy.local'],
            ['key'=>'lukman','name'=>'Lukman Hakim','email'=>'lukman@worqsy.local'],
            ['key'=>'dela','name'=>'Dela Putri','email'=>'dela@worqsy.local'],
            ['key'=>'alif','name'=>'Alif Pratama','email'=>'alif@client.local'],
        ])->mapWithKeys(function ($row) {
            $key=$row['key']; unset($row['key']);
            $user=User::create($row + ['password'=>Hash::make(config('worqsy.demo_password','password')),'status'=>'ACTIVE']);
            return [$key=>$user];
        });

        $workspace=Workspace::create([
            'name'=>'PT ABC Technology',
            'slug'=>'pt-abc-technology',
            'status'=>'ACTIVE',
            'created_by'=>$users['raka']->id,
        ]);

        $owner=WorkspaceMembership::create(['workspace_id'=>$workspace->id,'user_id'=>$users['budi']->id,'organization_role'=>'OWNER','company_position'=>'Company Owner','status'=>'ACTIVE','joined_at'=>now()]);
        $admin=WorkspaceMembership::create(['workspace_id'=>$workspace->id,'user_id'=>$users['raka']->id,'organization_role'=>'ADMIN','company_position'=>'CTO','scope'=>'Technology','status'=>'ACTIVE','joined_at'=>now()]);
        WorkspaceAdminPermission::create([
            'workspace_membership_id'=>$admin->id,
            'create_project'=>true,'manage_members'=>true,'manage_admins'=>true,'manage_guests'=>true,
            'manage_project_managers'=>true,'manage_permissions'=>true,'manage_lifecycle'=>true,
            'view_audit'=>true,'manage_billing'=>false,'manage_organization_settings'=>true,
        ]);
        foreach (['abdul'=>'Project Manager','arif'=>'Backend Lead','lukman'=>'Backend Developer','dela'=>'UI/UX Designer'] as $key=>$position) {
            WorkspaceMembership::create(['workspace_id'=>$workspace->id,'user_id'=>$users[$key]->id,'organization_role'=>'MEMBER','company_position'=>$position,'status'=>'ACTIVE','joined_at'=>now()]);
        }
        WorkspaceMembership::create(['workspace_id'=>$workspace->id,'user_id'=>$users['alif']->id,'organization_role'=>'GUEST','company_position'=>'Client','status'=>'ACTIVE','joined_at'=>now()]);

        $identity=app(ProjectIdentityService::class);

        $project=Project::create([
            'workspace_id'=>$workspace->id,'project_code'=>'WEB201','name'=>'Company Website',
            'description'=>'Corporate website delivery followed by ongoing maintenance and controlled future development.',
            'project_type'=>'Software Development','visibility'=>'PRIVATE','current_phase'=>'MAINTENANCE','status'=>'ACTIVE',
            'created_by'=>$users['raka']->id,'start_date'=>now()->subMonths(2)->toDateString(),'target_completion'=>now()->addMonth()->toDateString(),
        ]);

        $positions=[];
        foreach (['Project Manager','Backend Lead','Backend Developer','UI/UX Designer','Client'] as $name) {
            $positions[$name]=ProjectPosition::create(['project_id'=>$project->id,'name'=>$name,'slug'=>Str::slug($name)]);
        }

        $members=[];
        $definitions=[
            'abdul'=>['PROJECT_MANAGER','Project Manager','STANDARD'],
            'arif'=>['LEAD','Backend Lead','STANDARD'],
            'lukman'=>['MEMBER','Backend Developer','STANDARD'],
            'dela'=>['MEMBER','UI/UX Designer','READ_ONLY'],
            'alif'=>['GUEST','Client','COMMENT_ONLY'],
        ];
        foreach ($definitions as $key=>[$role,$position,$mode]) {
            $members[$key]=ProjectMembership::create([
                'project_id'=>$project->id,'user_id'=>$users[$key]->id,'project_role'=>$role,
                'project_position_id'=>$positions[$position]->id,'project_handle'=>$identity->make($users[$key],$project,$position),
                'permission_mode'=>$mode,'status'=>'ACTIVE',
            ]);
        }

        $create=ProjectPhaseCycle::create(['project_id'=>$project->id,'phase_type'=>'CREATE','cycle_number'=>1,'title'=>'Initial Create','started_at'=>now()->subMonths(2),'completed_at'=>now()->subDays(10),'status'=>'COMPLETED']);
        $maintenance=ProjectPhaseCycle::create(['project_id'=>$project->id,'phase_type'=>'MAINTENANCE','cycle_number'=>1,'title'=>'Maintenance #1','started_at'=>now()->subDays(10),'status'=>'ACTIVE']);
        foreach ($members as $key=>$membership) {
            ProjectPhaseAccess::create(['phase_cycle_id'=>$create->id,'project_membership_id'=>$membership->id,'access_status'=>'ACTIVE','activated_at'=>$create->started_at,'deactivated_at'=>$create->completed_at]);
            ProjectPhaseAccess::create(['phase_cycle_id'=>$maintenance->id,'project_membership_id'=>$membership->id,'access_status'=>in_array($key,['abdul','arif','lukman','alif'])?'ACTIVE':'INACTIVE','activated_at'=>in_array($key,['abdul','arif','lukman','alif'])?now()->subDays(10):null,'deactivated_at'=>$key==='dela'?now()->subDays(10):null]);
        }

        $taskData=[
            ['WEB201-001','Set up maintenance monitoring','APPROVED','MEDIUM',2,now()->subDays(4)],
            ['WEB201-002','Fix duplicate transaction validation','REVISION','HIGH',3,now()->addDays(2)],
            ['WEB201-003','Optimize homepage asset loading','IN_PROGRESS','MEDIUM',2,now()->addDays(4)],
            ['WEB201-004','Client content update review','REVIEWING','LOW',1,now()->addDays(1)],
            ['WEB201-005','Prepare monthly technical report','ASSIGNED','MEDIUM',1,now()->addDays(7)],
        ];
        $tasks=[];
        foreach ($taskData as [$code,$title,$status,$priority,$weight,$due]) {
            $task=Task::create([
                'project_id'=>$project->id,'phase_cycle_id'=>$maintenance->id,'task_code'=>$code,'title'=>$title,
                'description'=>'Seeded Worqsy demonstration task for the V1 workflow.','status'=>$status,'priority'=>$priority,'weight'=>$weight,
                'guest_visible'=>$code==='WEB201-004',
                'start_date'=>now()->subDays(3)->toDateString(),'due_date'=>$due,'created_by'=>$users['abdul']->id,
                'approved_at'=>$status==='APPROVED'?now()->subDay():null,
            ]);
            $task->assignees()->attach($users['lukman']->id);
            $task->reviewers()->attach($users['arif']->id);
            $task->approvers()->attach($users['abdul']->id);
            $tasks[$code]=$task;
        }

        $revision=TaskRevision::create([
            'task_id'=>$tasks['WEB201-002']->id,'revision_number'=>1,'requested_by'=>$users['arif']->id,
            'overall_deadline'=>now()->addDays(2),'status'=>'OPEN','request_note'=>'Validation must prevent duplicate transaction submissions.',
        ]);
        RevisionItem::create(['revision_id'=>$revision->id,'description'=>'Fix duplicate transaction validation.','priority'=>'HIGH','urgency'=>'ASAP','deadline'=>now()->addDay(),'status'=>'IN_PROGRESS']);
        RevisionItem::create(['revision_id'=>$revision->id,'description'=>'Improve error response copy.','priority'=>'LOW','urgency'=>'ASAP','deadline'=>now()->addDays(2),'status'=>'OPEN']);

        $project2=Project::create([
            'workspace_id'=>$workspace->id,'project_code'=>'APP305','name'=>'Mobile Application',
            'description'=>'Customer mobile application currently in a development cycle.',
            'project_type'=>'Software Development','visibility'=>'PRIVATE','current_phase'=>'DEVELOPMENT','status'=>'ACTIVE',
            'created_by'=>$users['raka']->id,'start_date'=>now()->subMonth()->toDateString(),'target_completion'=>now()->addMonths(2)->toDateString(),
        ]);
        $pmPos=ProjectPosition::create(['project_id'=>$project2->id,'name'=>'Project Manager','slug'=>'project-manager']);
        $leadPos=ProjectPosition::create(['project_id'=>$project2->id,'name'=>'Backend Lead','slug'=>'backend-lead']);
        $pm2=ProjectMembership::create(['project_id'=>$project2->id,'user_id'=>$users['abdul']->id,'project_role'=>'PROJECT_MANAGER','project_position_id'=>$pmPos->id,'project_handle'=>$identity->make($users['abdul'],$project2,'Project Manager'),'permission_mode'=>'STANDARD','status'=>'ACTIVE']);
        $lead2=ProjectMembership::create(['project_id'=>$project2->id,'user_id'=>$users['lukman']->id,'project_role'=>'LEAD','project_position_id'=>$leadPos->id,'project_handle'=>$identity->make($users['lukman'],$project2,'Backend Lead'),'permission_mode'=>'STANDARD','status'=>'ACTIVE']);
        $dev=ProjectPhaseCycle::create(['project_id'=>$project2->id,'phase_type'=>'DEVELOPMENT','cycle_number'=>1,'title'=>'Development #1','started_at'=>now()->subDays(8),'status'=>'ACTIVE']);
        foreach([$pm2,$lead2] as $m) ProjectPhaseAccess::create(['phase_cycle_id'=>$dev->id,'project_membership_id'=>$m->id,'access_status'=>'ACTIVE','activated_at'=>now()->subDays(8)]);

        $t=Task::create(['project_id'=>$project2->id,'phase_cycle_id'=>$dev->id,'task_code'=>'APP305-001','title'=>'Implement API authentication refresh flow','description'=>'Implement and validate token refresh behavior.','status'=>'IN_PROGRESS','priority'=>'HIGH','weight'=>3,'start_date'=>now()->subDay()->toDateString(),'due_date'=>now()->addDays(5),'created_by'=>$users['abdul']->id]);
        $t->assignees()->attach($users['lukman']->id); $t->reviewers()->attach($users['abdul']->id); $t->approvers()->attach($users['abdul']->id);
    }
}
