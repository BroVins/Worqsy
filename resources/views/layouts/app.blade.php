<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title','Worqsy') · Worqsy</title>

    <link rel="stylesheet" href="{{ asset('css/worqsy.css') }}">
</head>

<body>

<div class="app-shell">

<aside class="sidebar">

    <a href="{{ route('home') }}" class="brand">
        <span class="brand-mark">W</span>
        <span>Worqsy</span>
    </a>


    @php
        $orgRole=$workspaceMembership?->organization_role?->value ?? 'MEMBER';
    @endphp


    <div class="nav-section">
        WORKSPACE
    </div>


    <nav>


        <a class="nav-link {{ request()->routeIs('home')?'active':'' }}"
           href="{{ route('home') }}">
            <span class="nav-icon">⌂</span>
            <span class="label">Dashboard</span>
        </a>


        <a class="nav-link {{ request()->routeIs('projects.*')?'active':'' }}"
           href="{{ route('projects.index') }}">
            <span class="nav-icon">▦</span>
            <span class="label">Projects</span>
        </a>


        <a class="nav-link {{ request()->routeIs('calendar.*')?'active':'' }}"
           href="{{ route('calendar.index') }}">
            <span class="nav-icon">□</span>
            <span class="label">Calendar</span>
        </a>



        <div class="nav-section">
            COLLABORATION
        </div>


        @if($orgRole==='MEMBER' || $orgRole==='ADMIN')

        <a class="nav-link {{ request()->routeIs('reviews.*')?'active':'' }}"
           href="{{ route('reviews.index') }}">
            <span class="nav-icon">✓</span>
            <span class="label">Reviews</span>
        </a>


        <a class="nav-link {{ request()->routeIs('revisions.*')?'active':'' }}"
           href="{{ route('revisions.index') }}">
            <span class="nav-icon">↻</span>
            <span class="label">Revisions</span>
        </a>


        <a class="nav-link {{ request()->routeIs('messages.*')?'active':'' }}"
           href="{{ route('messages.index') }}">
            <span class="nav-icon">◌</span>
            <span class="label">Messages</span>
        </a>

        @endif



        <a class="nav-link {{ request()->routeIs('notifications.*')?'active':'' }}"
           href="{{ route('notifications.index') }}">
            <span class="nav-icon">•</span>
            <span class="label">Notifications</span>
        </a>



        @if(in_array($orgRole,['OWNER','ADMIN']))

        <div class="nav-section">
            MANAGEMENT
        </div>


        <a class="nav-link {{ request()->routeIs('reports.*')?'active':'' }}"
           href="{{ route('reports.index') }}">
            <span class="nav-icon">◫</span>
            <span class="label">Reports</span>
        </a>


        <a class="nav-link {{ request()->routeIs('people.*')?'active':'' }}"
           href="{{ route('people.index') }}">
            <span class="nav-icon">◎</span>
            <span class="label">People</span>
        </a>


        <a class="nav-link {{ request()->routeIs('audit.*')?'active':'' }}"
           href="{{ route('audit.index') }}">
            <span class="nav-icon">≡</span>
            <span class="label">Audit</span>
        </a>


        @endif


    </nav>



    <div class="sidebar-bottom">

        <div class="user-mini">

            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>


            <div class="user-copy">

                <div style="font-size:13px;font-weight:700">
                    {{ auth()->user()->name }}
                </div>

                <div style="font-size:11px;color:#94a3b8">
                    {{ $orgRole }}
                </div>

            </div>

        </div>

    </div>


</aside>



<main class="main">


<header class="topbar">


<div class="flex items-center gap-3">


<form method="POST"
      action="{{ route('workspace.switch',$currentWorkspace) }}">

@csrf

<select class="workspace-switcher"
onchange="if(this.value){this.form.action='/workspace/'+this.value+'/switch';this.form.submit()}">

@foreach(auth()->user()->workspaceMemberships()->where('status','ACTIVE')->with('workspace')->get() as $wm)

<option value="{{ $wm->workspace_id }}"
@selected($wm->workspace_id===$currentWorkspace->id)>
{{ $wm->workspace->name }}
</option>

@endforeach

</select>

</form>


<form method="GET"
      action="{{ route('search') }}">

<input class="input"
style="width:280px;padding:9px 12px"
name="q"
value="{{ request('q') }}"
placeholder="Search projects, tasks, files...">

</form>


</div>



<div class="flex items-center gap-3">


<span class="muted" style="font-size:13px">
{{ now()->format('d M Y') }}
</span>


<div class="avatar">
{{ strtoupper(substr(auth()->user()->name,0,1)) }}
</div>


<form method="POST" action="{{ route('logout') }}">

@csrf

<button class="btn btn-secondary btn-sm">
Sign out
</button>

</form>


</div>


</header>



<div class="content">


@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


@if($errors->any())

<div class="alert alert-error">

<strong>Please check the form.</strong>

<ul>

@foreach($errors->all() as $e)

<li>{{ $e }}</li>

@endforeach

</ul>

</div>

@endif


@yield('content')


</div>


</main>

</div>


<script src="{{ asset('js/worqsy.js') }}"></script>

@stack('scripts')


</body>
</html>
