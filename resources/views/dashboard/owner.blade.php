@extends('layouts.app')

@section('title','Workspace Overview')

@section('content')

<div class="page-head">
    <div>
        <h1 class="page-title">
            Good afternoon, {{ auth()->user()->name ?? 'Owner' }} 👋
        </h1>

        <p class="page-subtitle">
            Monitor workspace performance, project health, and overall progress.
        </p>
    </div>
</div>


<div class="grid grid-4">

<div class="card stat">
    <div class="stat-label">Total Projects</div>
    <div class="stat-value">{{ $projects->count() }}</div>
    <div class="stat-note">Workspace projects</div>
</div>


<div class="card stat">
    <div class="stat-label">Healthy Projects</div>
    <div class="stat-value">
        {{ collect($cards)->where('health','HEALTHY')->count() }}
    </div>
    <div class="stat-note">Running normally</div>
</div>


<div class="card stat">
    <div class="stat-label">Active Workspace</div>
    <div class="stat-value">
        {{ $workspace->name }}
    </div>
</div>


<div class="card stat">
    <div class="stat-label">Projects Tracked</div>
    <div class="stat-value">
        {{ count($cards) }}
    </div>
</div>

</div>



<section class="card mt-4">

<div class="section-head">
    <h2 class="section-title">
        Project Health Overview
    </h2>
</div>


<div class="grid grid-3 card-pad">


@forelse($cards as $row)

<a href="{{ route('projects.show',$row['project']) }}"
class="card project-card">


<div class="project-code">
{{ $row['project']->project_code }}
</div>


<div class="project-name">
{{ $row['project']->name }}
</div>


<div class="meta-row">

<x-badge :value="$row['health']"/>

</div>


<div>

<div style="display:flex;justify-content:space-between;font-size:13px">

<span class="muted">
Progress
</span>

<strong>
{{ number_format($row['progress'],0) }}%
</strong>

</div>


<x-progress :value="$row['progress']"/>

</div>


</a>


@empty

<div class="empty">
No projects available.
</div>

@endforelse


</div>

</section>



<section class="card mt-4">

<div class="section-head">
<h2 class="section-title">
Workspace Activity
</h2>
</div>


<div class="card-pad muted">

✓ Project monitoring enabled

<br><br>

✓ Workspace overview available

<br><br>

✓ Team collaboration tracking active

</div>


</section>


@endsection
