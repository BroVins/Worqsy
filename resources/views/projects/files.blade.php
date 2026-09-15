@extends('layouts.app')
@section('title',$project->name.' · Files')
@section('content')
<div class="page-head"><div><div class="project-code">{{ $project->project_code }}</div><h1 class="page-title">{{ $project->name }}</h1></div></div>
<x-project-tabs :project="$project" active="files" />
<div class="grid grid-2 mt-4">
<section class="card">
  <div class="section-head"><h2 class="section-title">Project Files</h2></div>
  <div class="table-wrap"><table class="table"><thead><tr><th>File</th><th>Uploaded by</th><th>Size</th><th></th></tr></thead><tbody>
  @forelse($links as $link)<tr><td><strong>{{ $link->file->original_name }}</strong><br><span class="muted">{{ $link->file->mime_type }}</span></td><td>{{ $link->file->uploader?->name }}</td><td>{{ number_format($link->file->size/1024,1) }} KB</td><td>@if($canDownload && ($isGuest ? $link->guest_can_download : $link->can_download))<a class="btn btn-secondary btn-sm" href="{{ route('projects.files.download',[$project,$link->file]) }}">Download</a>@else<span class="muted">View only</span>@endif</td></tr>
  @empty<tr><td colspan="4"><div class="empty">No files uploaded.</div></td></tr>@endforelse
  </tbody></table></div>
</section>
@if($canUpload)
<section class="card">
  <div class="section-head"><h2 class="section-title">Upload File</h2></div>
  <form class="card-pad" method="POST" enctype="multipart/form-data" action="{{ route('projects.files.store',$project) }}">@csrf
    <div class="form-group"><label class="label">File</label><input class="input" type="file" name="file" required><span class="hint">Maximum 10 MB. Access follows project authorization.</span></div>
    <label class="mt-3" style="display:flex;gap:8px;align-items:center;font-size:13px"><input type="checkbox" name="guest_visible" value="1"> Visible to project Guests</label>
    <label class="mt-2" style="display:flex;gap:8px;align-items:center;font-size:13px"><input type="checkbox" name="guest_can_download" value="1"> Allow download when external download permission is enabled</label>
    <button class="btn btn-primary mt-3">Upload</button>
  </form>
</section>
@else
<section class="card"><div class="empty">You have read-only file access.</div></section>
@endif
</div>
@endsection
