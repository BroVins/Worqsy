@extends('layouts.app')
@section('title','People')
@section('content')
<div class="page-head"><div><h1 class="page-title">People</h1><p class="page-subtitle">Workspace members, organization roles, positions, and account state.</p></div></div>
<section class="card">
<div class="table-wrap"><table class="table"><thead><tr><th>Person</th><th>Organization Role</th><th>Position</th><th>Scope</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($people as $p)<tr><td><strong>{{ $p->user->name }}</strong><br><span class="muted">{{ $p->user->email }}</span></td><td><x-badge :value="$p->organization_role" /></td><td>{{ $p->company_position ?: '—' }}</td><td>{{ $p->scope ?: '—' }}</td><td><x-badge :value="$p->status" /></td><td>
@if(($workspaceMembership?->organization_role?->value ?? '')==='ADMIN' && ($workspaceMembership?->adminPermission?->manage_members))
  <div class="flex gap-2">
    <a class="btn btn-secondary btn-sm" href="{{ route('people.edit',$p) }}">Edit</a>
    @if($p->status->value==='ACTIVE' && $p->user_id!==auth()->id())
    <form method="POST" action="{{ route('people.revoke',$p) }}">@csrf<button class="btn btn-danger btn-sm" data-confirm="Revoke workspace access?">Revoke</button></form>
    @elseif($p->status->value!=='ACTIVE')
    <form method="POST" action="{{ route('people.reactivate',$p) }}">@csrf<button class="btn btn-secondary btn-sm">Reactivate</button></form>
    @endif
  </div>
@endif
</td></tr>@endforeach
</tbody></table></div>
</section>


@if(($workspaceMembership?->organization_role?->value ?? '')==='ADMIN' && ($workspaceMembership?->adminPermission?->manage_members))
<section class="card mt-4">
<div class="section-head"><h2 class="section-title">Pre-register Internal Member</h2></div>
<form class="card-pad" method="POST" action="{{ route('people.internal.store') }}">@csrf
<div class="form-grid">
 <div class="form-group"><label class="label">Name</label><input class="input" name="name" required></div>
 <div class="form-group"><label class="label">Google Email</label><input class="input" type="email" name="email" required></div>
 <div class="form-group"><label class="label">Organization Role</label><select class="select" name="organization_role"><option>MEMBER</option><option>OWNER</option>@if($workspaceMembership?->adminPermission?->manage_admins)<option>ADMIN</option>@endif</select></div>
 <div class="form-group"><label class="label">Company Position</label><input class="input" name="company_position" placeholder="Finance Staff"></div>
 <div class="form-group"><label class="label">Scope (optional)</label><input class="input" name="scope" placeholder="Technology"></div>
</div>
<button class="btn btn-primary mt-3">Pre-register Member</button>
<p class="hint mt-3">The person activates the existing account/membership by signing in with the same Google email.</p>
</form>
</section>
@endif

@if(($workspaceMembership?->organization_role?->value ?? '')==='ADMIN' && ($workspaceMembership?->adminPermission?->manage_guests))
<section class="card mt-4">
<div class="section-head"><h2 class="section-title">Invite External Guest</h2></div>
<form class="card-pad" method="POST" action="{{ route('invitations.store') }}">@csrf
<div class="form-grid">
 <div class="form-group"><label class="label">Google Email</label><input class="input" type="email" name="email" required></div>
 <div class="form-group"><label class="label">Guest Type</label><select class="select" name="guest_type">@foreach(['Client','Vendor','Freelancer','Consultant','Partner'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
 <div class="form-group"><label class="label">Project ID (optional)</label><input class="input" name="project_id" placeholder="UUID"></div>
 <div class="form-group"><label class="label">Permission Mode</label><select class="select" name="permission_mode"><option>COMMENT_ONLY</option><option>READ_ONLY</option><option>RESTRICTED</option></select></div>
</div>
<button class="btn btn-primary mt-3">Create Invitation</button>
</form>
</section>
@endif
@endsection
