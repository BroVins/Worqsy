@extends('layouts.app')
@section('title','Edit Member')
@section('content')
<div class="page-head"><div><h1 class="page-title">Edit Workspace Member</h1><p class="page-subtitle">{{ $membership->user->name }} · {{ $membership->user->email }}</p></div><a class="btn btn-secondary" href="{{ route('people.index') }}">Back</a></div>
<form class="card card-pad" method="POST" action="{{ route('people.update',$membership) }}">@csrf @method('PUT')
<div class="form-grid">
 <div class="form-group"><label class="label">Organization Role</label><select class="select" name="organization_role">@foreach(['OWNER','ADMIN','MEMBER','GUEST'] as $r)<option value="{{ $r }}" @selected($membership->organization_role->value===$r)>{{ $r }}</option>@endforeach</select></div>
 <div class="form-group"><label class="label">Status</label><select class="select" name="status">@foreach(['ACTIVE','INACTIVE','REVOKED'] as $v)<option value="{{ $v }}" @selected($membership->status->value===$v)>{{ $v }}</option>@endforeach</select></div>
 <div class="form-group"><label class="label">Company Position</label><input class="input" name="company_position" value="{{ $membership->company_position }}"></div>
 <div class="form-group"><label class="label">Scope</label><input class="input" name="scope" value="{{ $membership->scope }}"></div>
</div>

@if($membership->organization_role->value==='ADMIN' || ($workspaceMembership?->adminPermission?->manage_admins))
<div class="card card-pad mt-4">
 <h3 style="margin-top:0">Admin Permissions</h3>
 <p class="hint">Permissions apply when the membership role is ADMIN. Unchecked permissions are denied.</p>
 <div class="grid grid-2">
 @foreach([
 'create_project'=>'Create Project','manage_members'=>'Manage Members','manage_admins'=>'Manage Admins',
 'manage_guests'=>'Manage Guests','manage_project_managers'=>'Manage Project Managers',
 'manage_permissions'=>'Manage Permissions','manage_lifecycle'=>'Manage Lifecycle',
 'view_audit'=>'View Audit','manage_billing'=>'Manage Billing',
 'manage_organization_settings'=>'Manage Organization Settings'
 ] as $field=>$label)
 <label style="display:flex;gap:9px;align-items:center;font-size:13px"><input type="checkbox" name="{{ $field }}" value="1" @checked((bool)($membership->adminPermission?->{$field}))> {{ $label }}</label>
 @endforeach
 </div>
</div>
@endif
<button class="btn btn-primary mt-4">Save Changes</button>
</form>
@endsection
