<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create Workspace · Worqsy</title><link rel="stylesheet" href="{{ asset('css/worqsy.css') }}"></head>
<body>
<div class="auth-page">
<section class="auth-hero"><div class="brand"><span class="brand-mark">W</span><span>Worqsy</span></div><div><h1>Create your first workspace.</h1><p>A workspace represents an organization. The creator is not automatically treated as the business Owner; the initial organization role is Administrator so the organization can be configured.</p></div><div style="color:#94a3b8;font-size:13px">People. Work. Structure. Progress.</div></section>
<section class="auth-panel"><div class="auth-box"><div class="auth-logo">Worqsy</div><h2>Workspace setup</h2><p class="muted">Signed in as {{ auth()->user()->email }}</p>
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('onboarding.workspace.store') }}">@csrf
<div class="form-group"><label class="label">Organization / Workspace Name</label><input class="input" name="name" required placeholder="PT ABC Technology"></div>
<div class="form-group mt-3"><label class="label">Your Position</label><input class="input" name="company_position" placeholder="CTO"></div>
<button class="btn btn-primary w-full mt-4" style="justify-content:center">Create Workspace</button>
</form></div></section>
</div>
</body></html>
