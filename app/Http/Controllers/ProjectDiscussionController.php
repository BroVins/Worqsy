<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectDiscussionController extends Controller
{
    public function index(Request $request, Project $project, PermissionService $permissions): View
    {
        abort_unless($permissions->canViewProject($request->user(),$project),403);
        $messages=$project->discussions()->with('user')->latest()->paginate(30);
        return view('projects.discussion',compact('project','messages'));
    }

    public function store(Request $request, Project $project, PermissionService $permissions): RedirectResponse
    {
        abort_unless($permissions->canComment($request->user(),$project),403);
        $data=$request->validate(['body'=>['required','string','max:5000']]);
        \App\Models\ProjectDiscussion::create(['project_id'=>$project->id,'user_id'=>$request->user()->id,'body'=>$data['body']]);
        return back()->with('success','Project update posted.');
    }
}
