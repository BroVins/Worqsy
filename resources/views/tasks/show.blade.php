@extends('layouts.app')
@section('title',$task->title)
@section('content')
<div class="page-head">
  <div><a class="project-code" href="{{ route('projects.show',$task->project) }}">{{ $task->project->project_code }} / {{ $task->task_code }}</a><h1 class="page-title">{{ $task->title }}</h1><div class="meta-row"><x-badge :value="$task->status" /><x-badge :value="$task->priority" /><span>Weight {{ $task->weight }}</span></div></div>
  <a class="btn btn-secondary" href="{{ route('projects.view',[$task->project,'list']) }}">Back to project</a>
</div>

<div class="grid grid-2">
<section class="card">
  <div class="section-head"><h2 class="section-title">Task Details</h2></div>
  <div class="card-pad">
    <p style="line-height:1.7">{{ $task->description ?: 'No description.' }}</p>
    <dl class="kv mt-4">
      <dt>Assignee</dt><dd>{{ $task->assignees->pluck('name')->join(', ') ?: '—' }}</dd>
      <dt>Reviewer</dt><dd>{{ $task->reviewers->pluck('name')->join(', ') ?: '—' }}</dd>
      <dt>Approver</dt><dd>{{ $task->approvers->pluck('name')->join(', ') ?: '—' }}</dd>
      <dt>Start date</dt><dd>{{ $task->start_date?->format('d M Y') ?? '—' }}</dd>
      <dt>Due date</dt><dd>{{ $task->due_date?->format('d M Y H:i') ?? '—' }}</dd>
      <dt>Approved at</dt><dd>{{ $task->approved_at?->format('d M Y H:i') ?? '—' }}</dd>
    </dl>
  </div>
</section>

<section class="card">
  <div class="section-head"><h2 class="section-title">Workflow Action</h2></div>
  <div class="card-pad">
  @if($task->status->value==='ASSIGNED' && $task->assignees->contains('id',auth()->id()))
    <form method="POST" action="{{ route('tasks.start',$task) }}">@csrf<button class="btn btn-primary">Start Task</button></form>
  @elseif($task->status->value==='IN_PROGRESS' && $task->assignees->contains('id',auth()->id()))
    <h3 style="margin-top:0">Submit as Done</h3>
    <form method="POST" action="{{ route('tasks.submit',$task) }}">@csrf
      <div class="form-group"><label class="label">Work Summary</label><textarea class="textarea" name="work_summary" required></textarea></div>
      <div class="form-group mt-3"><label class="label">Deliverables</label><textarea class="textarea" name="deliverables_text" placeholder="One deliverable per line"></textarea></div>
      <div class="form-group mt-3"><label class="label">Links</label><textarea class="textarea" name="links_text" placeholder="One URL per line"></textarea></div>
      <div class="form-group mt-3"><label class="label">Completion Notes</label><textarea class="textarea" name="completion_notes"></textarea></div>
      <button class="btn btn-primary mt-3">Submit for Review</button>
    </form>
  @elseif($task->status->value==='REVIEWING' && ($task->reviewers->contains('id',auth()->id()) || $task->approvers->contains('id',auth()->id())))
    <form method="POST" action="{{ route('tasks.approve',$task) }}">@csrf<button class="btn btn-primary">Approve Work</button></form>
    <hr style="border:0;border-top:1px solid var(--line);margin:22px 0">
    <h3>Request Revision</h3>
    <form method="POST" action="{{ route('tasks.revision',$task) }}">@csrf
      <div class="form-group"><label class="label">Overall Deadline</label><input class="input" type="datetime-local" name="overall_deadline"></div>
      <div class="form-group mt-3"><label class="label">Request Note</label><textarea class="textarea" name="request_note"></textarea></div>
      <div class="card card-pad mt-3">
        <strong>Revision Item #1</strong>
        <div class="form-group mt-3"><label class="label">Description</label><textarea class="textarea" name="items[0][description]" required></textarea></div>
        <div class="form-grid mt-3">
          <div class="form-group"><label class="label">Priority</label><select class="select" name="items[0][priority]">@foreach(['LOW','MEDIUM','HIGH','CRITICAL'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
          <div class="form-group"><label class="label">Urgency</label><select class="select" name="items[0][urgency]">@foreach(['NORMAL','ASAP','IMMEDIATE'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
          <div class="form-group full"><label class="label">Item Deadline</label><input class="input" type="datetime-local" name="items[0][deadline]"></div>
        </div>
      </div>
      <button class="btn btn-danger mt-3">Request Revision</button>
    </form>
  @elseif($task->status->value==='APPROVED')
    <div class="alert alert-success mb-0"><strong>Approved.</strong> This task counts as completed project progress.</div>
  @else
    <p class="muted">No workflow action is available for your current responsibility and task status.</p>
  @endif
  </div>
</section>
</div>

<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">Subtasks</h2><span class="hint">{{ $task->subtasks->where('is_completed',true)->count() }}/{{ $task->subtasks->count() }} completed</span></div>
  <div class="card-pad">
    @forelse($task->subtasks as $subtask)
    <div class="flex justify-between items-center" style="padding:10px 0;border-bottom:1px solid var(--line)">
      <div style="{{ $subtask->is_completed?'text-decoration:line-through;color:var(--muted)':'' }}">{{ $subtask->title }}</div>
      @if($task->assignees->contains('id',auth()->id()))
      <form method="POST" action="{{ route('subtasks.toggle',$subtask) }}">@csrf<button class="btn btn-secondary btn-sm">{{ $subtask->is_completed?'Reopen':'Complete' }}</button></form>
      @else
      <x-badge :value="$subtask->is_completed?'APPROVED':'ASSIGNED'" />
      @endif
    </div>
    @empty<p class="muted">No subtasks.</p>@endforelse

    @if($task->assignees->contains('id',auth()->id()) || in_array($workspaceMembership?->organization_role?->value ?? '',['ADMIN']))
    <form class="mt-4 flex gap-2" method="POST" action="{{ route('tasks.subtasks.store',$task) }}">@csrf
      <input class="input" name="title" placeholder="Add subtask..." required>
      <button class="btn btn-secondary">Add</button>
    </form>
    @endif
  </div>
</section>

@if($task->revisions->isNotEmpty())
<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">Structured Revisions</h2></div>
  @foreach($task->revisions->sortByDesc('revision_number') as $revision)
  <div class="card-pad" style="border-bottom:1px solid var(--line)">
    <div class="flex justify-between items-center"><div><strong>Revision #{{ str_pad($revision->revision_number,2,'0',STR_PAD_LEFT) }}</strong><div class="hint">Overall deadline: {{ $revision->overall_deadline?->format('d M Y H:i') ?? '—' }}</div></div><x-badge :value="$revision->status" /></div>
    @if($revision->request_note)<p>{{ $revision->request_note }}</p>@endif
    <div class="table-wrap mt-3"><table class="table"><thead><tr><th>Item</th><th>Priority</th><th>Urgency</th><th>Deadline</th><th>Status</th></tr></thead><tbody>
      @foreach($revision->items as $item)
      <tr><td>{{ $item->description }}</td><td><x-badge :value="$item->priority" /></td><td>{{ $item->urgency->value }}</td><td>{{ $item->deadline?->format('d M Y H:i') ?? '—' }}</td><td>
        @if($task->assignees->contains('id',auth()->id()) && $revision->status==='OPEN')
        <form method="POST" action="{{ route('revision-items.update',$item) }}">@csrf @method('PATCH')<select class="select" name="status" onchange="this.form.submit()">@foreach(['OPEN','IN_PROGRESS','DONE'] as $s)<option value="{{ $s }}" @selected($item->status->value===$s)>{{ $s }}</option>@endforeach</select></form>
        @else <x-badge :value="$item->status" /> @endif
      </td></tr>
      @endforeach
    </tbody></table></div>
    @if($task->assignees->contains('id',auth()->id()) && $revision->status==='OPEN')
      <form class="mt-3" method="POST" action="{{ route('revisions.resubmit',$revision) }}">@csrf<button class="btn btn-primary">Resubmit Revision</button></form>
    @endif
  </div>
  @endforeach
</section>
@endif

@if($task->submissions->isNotEmpty())
<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">Submission History</h2></div>
  <div class="table-wrap"><table class="table"><thead><tr><th>#</th><th>Submitted By</th><th>Summary</th><th>Submitted At</th></tr></thead><tbody>
  @foreach($task->submissions->sortByDesc('submission_number') as $s)<tr><td>#{{ $s->submission_number }}</td><td>{{ $s->submitter->name }}</td><td>{{ $s->work_summary }}</td><td>{{ $s->submitted_at->format('d M Y H:i') }}</td></tr>@endforeach
  </tbody></table></div>
</section>
@endif

<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">Task History</h2></div>
  <div class="table-wrap"><table class="table"><thead><tr><th>Time</th><th>Actor</th><th>Event</th><th>Change</th></tr></thead><tbody>
  @forelse($history as $event)
  <tr><td>{{ $event->created_at?->format('d M Y H:i:s') }}</td><td>{{ $event->actor?->name ?: 'System' }}</td><td><strong>{{ $event->event_type }}</strong></td><td><span class="muted">{{ $event->before_data ? json_encode($event->before_data) : '' }}</span> @if($event->after_data) → {{ json_encode($event->after_data) }} @endif</td></tr>
  @empty<tr><td colspan="4"><div class="empty">No recorded task history yet.</div></td></tr>@endforelse
  </tbody></table></div>
</section>

<section class="card mt-4">
  <div class="section-head"><h2 class="section-title">Comments</h2></div>
  <div class="card-pad">
    @forelse($task->comments as $comment)<div style="padding:12px 0;border-bottom:1px solid var(--line)"><div class="flex justify-between"><strong>{{ $comment->user->name }}</strong><span class="hint">{{ $comment->created_at->diffForHumans() }}</span></div><p class="mb-0">{{ $comment->body }}</p></div>@empty<p class="muted">No comments yet.</p>@endforelse
    <form class="mt-4" method="POST" action="{{ route('tasks.comments.store',$task) }}">@csrf<div class="form-group"><label class="label">Add comment</label><textarea class="textarea" name="body" required></textarea></div><button class="btn btn-secondary mt-3">Comment</button></form>
  </div>
</section>
@endsection
