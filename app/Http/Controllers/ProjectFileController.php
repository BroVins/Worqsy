<?php
namespace App\Http\Controllers;

use App\Models\FileLink;
use App\Models\Project;
use App\Models\StoredFile;
use App\Services\PermissionService;
use App\Services\AccessTracker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectFileController extends Controller
{
    public function index(Request $request, Project $project, PermissionService $permissions): View
    {
        abort_unless($permissions->canViewProject($request->user(),$project),403);
        $links=FileLink::where('resource_type','project')->where('resource_id',$project->id)
            ->when($permissions->isProjectGuest($request->user(),$project),fn($q)=>$q->where('guest_visible',true))
            ->with('file.uploader')->latest()->get();
        $canUpload=$permissions->canUploadProjectFile($request->user(),$project);
        $canDownload=$permissions->canDownloadProjectFile($request->user(),$project);
        $isGuest=$permissions->isProjectGuest($request->user(),$project);
        return view('projects.files',compact('project','links','canUpload','canDownload','isGuest'));
    }

    public function store(Request $request, Project $project, PermissionService $permissions): RedirectResponse
    {
        abort_unless($permissions->canUploadProjectFile($request->user(),$project),403);
        $data=$request->validate(['file'=>['required','file','max:10240'],'guest_visible'=>['nullable','boolean'],'guest_can_download'=>['nullable','boolean']]);
        $upload=$request->file('file');
        $path=$upload->store('projects/'.$project->id,'public');
        $file=StoredFile::create([
            'workspace_id'=>$project->workspace_id,'uploaded_by'=>$request->user()->id,'disk'=>'public','path'=>$path,
            'original_name'=>$upload->getClientOriginalName(),'mime_type'=>$upload->getMimeType(),'size'=>$upload->getSize(),'visibility'=>'PRIVATE',
        ]);
        FileLink::create(['file_id'=>$file->id,'resource_type'=>'project','resource_id'=>$project->id,'can_download'=>true,'guest_visible'=>(bool)($data['guest_visible']??false),'guest_can_download'=>(bool)($data['guest_can_download']??false)]);
        return back()->with('success','File uploaded.');
    }

    public function download(Request $request, Project $project, StoredFile $file, PermissionService $permissions, AccessTracker $access): StreamedResponse
    {
        abort_unless($permissions->canDownloadProjectFile($request->user(),$project),403);
        $link=FileLink::where('file_id',$file->id)->where('resource_type','project')->where('resource_id',$project->id)->firstOrFail();
        if ($permissions->isProjectGuest($request->user(),$project)) {
            abort_unless($link->guest_visible && $link->guest_can_download,403);
        } else {
            abort_unless($link->can_download,403);
        }
        $access->touch($request->user(),$project->workspace,'file',$file->id);
        return Storage::disk($file->disk)->download($file->path,$file->original_name);
    }
}
