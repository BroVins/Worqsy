@extends('layouts.app')

@section('title','Admin Dashboard')

@section('content')

<div class="page-head">

<div>

<h1 class="page-title">
Good afternoon, {{ auth()->user()->name ?? 'Admin' }} 👋
</h1>

<p class="page-subtitle">
Manage projects, reviews, revisions, and workspace operations.
</p>

</div>

</div>



<div class="grid grid-4">


<div class="card stat">
<div class="stat-label">
Projects
</div>

<div class="stat-value">
{{ $projects->count() }}
</div>

<div class="stat-note">
Active workspace
</div>

</div>



<div class="card stat">
<div class="stat-label">
Review Backlog
</div>

<div class="stat-value">
{{ $reviewBacklog }}
</div>

<div class="stat-note">
Need approval
</div>

</div>



<div class="card stat">
<div class="stat-label">
Revision Needed
</div>

<div class="stat-value">
{{ $revisionBacklog }}
</div>

<div class="stat-note">
Require changes
</div>

</div>



<div class="card stat">
<div class="stat-label">
Guest Members
</div>

<div class="stat-value">
{{ $guestCount }}
</div>

<div class="stat-note">
External access
</div>

</div>


</div>



<section class="card mt-4">


<div class="section-head">

<h2 class="section-title">
Project Monitoring
</h2>

</div>



<div class="grid grid-3 card-pad">


@foreach($cards as $row)

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

@endforeach


</div>


</section>



<section class="card mt-4">


<div class="section-head">

<h2 class="section-title">
Operational Queue
</h2>

</div>


<div class="card-pad muted">

Review tasks waiting:
<strong>{{ $reviewBacklog }}</strong>

<br><br>

Revision tasks:
<strong>{{ $revisionBacklog }}</strong>

</div>


</section>


@endsection
