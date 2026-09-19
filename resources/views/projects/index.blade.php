@extends('layouts.app')

@section('title','Projects')

@section('content')

<div class="page-head">
    <div>
        <h1 class="page-title">Projects</h1>
        <p class="page-subtitle">
            Manage your workspace projects and track progress.
        </p>
    </div>

    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        + Create Project
    </a>
</div>


<div class="card" style="padding:16px;margin-bottom:20px;">
    <input
        type="text"
        placeholder="Search projects..."
        style="
            width:100%;
            padding:12px 14px;
            border:1px solid #dbe2ea;
            border-radius:10px;
            font-size:14px;
        "
    >
</div>


<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:20px;
">


@forelse($projects as $row)

@php
    $project = $row['project'];
    $progress = $row['progress'] ?? 0;
    $health = $row['health'] ?? null;
@endphp


<a href="{{ route('projects.show',$project) }}"
   class="card"
   style="
      padding:22px;
      text-decoration:none;
      color:inherit;
      transition:.2s;
   ">


<div style="display:flex;justify-content:space-between;gap:15px;">

    <div>
        <small style="color:#64748b;">
            {{ $project->project_code }}
        </small>

        <h2 style="
            margin:8px 0;
            font-size:20px;
        ">
            {{ $project->name }}
        </h2>
    </div>

    @if($health)
        <x-badge :value="$health"/>
    @endif

</div>


<p style="
    color:#64748b;
    min-height:45px;
    line-height:1.5;
">
{{ Str::limit($project->description ?? 'No description available.',110) }}
</p>



<div style="margin-top:20px;">

<div style="
display:flex;
justify-content:space-between;
font-size:13px;
margin-bottom:8px;
">

<span>Progress</span>

<strong>{{ number_format($progress,0) }}%</strong>

</div>


<div style="
height:8px;
background:#e2e8f0;
border-radius:20px;
overflow:hidden;
">

<div style="
height:100%;
width:{{ $progress }}%;
background:#4f46e5;
">
</div>

</div>

</div>



<div style="
margin-top:18px;
font-size:13px;
color:#475569;
">

👥 Team collaboration

</div>



<div style="
margin-top:18px;
color:#2563eb;
font-weight:600;
">

View Project →

</div>


</a>


@empty

<div class="card" style="padding:40px;text-align:center;">
    <div style="font-size:36px;">📁</div>
    <h3>No projects yet</h3>
    <p>Create your first project to start collaborating.</p>
</div>

@endforelse


</div>

@endsection
