<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectActivityController;
use App\Http\Controllers\ProjectDiscussionController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\ProjectMembersPageController;
use App\Http\Controllers\RevisionInboxController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InternalMemberController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\ProjectPhaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskSubmissionController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) return view('welcome');
    return auth()->user()->workspaceMemberships()->where('status','ACTIVE')->exists()
        ? redirect()->route('home')
        : redirect()->route('onboarding.workspace');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login/demo', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1')->name('login.demo');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->middleware('throttle:30,1')->name('auth.google.callback');
});


Route::middleware(['auth','account.active'])->group(function () {
    Route::get('/onboarding/workspace', [OnboardingController::class,'create'])->name('onboarding.workspace');
    Route::post('/onboarding/workspace', [OnboardingController::class,'store'])->name('onboarding.workspace.store');
});

Route::middleware(['auth','account.active','workspace'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/home', HomeController::class)->name('home');
    Route::get('/search', SearchController::class)->name('search');

    Route::get('/calendar', CalendarController::class)->name('calendar.index');
    Route::get('/revisions', RevisionInboxController::class)->name('revisions.index');

    Route::get('/notifications', [NotificationController::class,'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class,'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class,'read'])->name('notifications.read');

    Route::get('/messages', [MessageController::class,'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class,'start'])->name('messages.start');
    Route::get('/messages/{conversation}', [MessageController::class,'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class,'send'])->name('messages.send');

    Route::post('/workspace/{workspace}/switch', [WorkspaceController::class,'switch'])->name('workspace.switch');

    Route::get('/projects', [ProjectController::class,'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class,'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class,'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class,'show'])->name('projects.show');
    Route::get('/projects/{project}/view/{view}', [ProjectController::class,'view'])->name('projects.view');
    Route::post('/projects/{project}/phase', [ProjectPhaseController::class,'transition'])->name('projects.phase.transition');
    Route::post('/projects/{project}/members', [ProjectMemberController::class,'store'])->name('projects.members.store');
    Route::post('/projects/{project}/members/{membership}/revoke', [ProjectMemberController::class,'revoke'])->name('projects.members.revoke');
    Route::post('/projects/{project}/members/{membership}/reactivate', [ProjectMemberController::class,'reactivate'])->name('projects.members.reactivate');

    Route::get('/projects/{project}/discussion', [ProjectDiscussionController::class,'index'])->name('projects.discussion');
    Route::post('/projects/{project}/discussion', [ProjectDiscussionController::class,'store'])->name('projects.discussion.store');
    Route::get('/projects/{project}/files', [ProjectFileController::class,'index'])->name('projects.files');
    Route::post('/projects/{project}/files', [ProjectFileController::class,'store'])->name('projects.files.store');
    Route::get('/projects/{project}/files/{file}/download', [ProjectFileController::class,'download'])->name('projects.files.download');
    Route::get('/projects/{project}/members', ProjectMembersPageController::class)->name('projects.members');
    Route::get('/projects/{project}/activity', [ProjectActivityController::class,'index'])->name('projects.activity');

    Route::get('/projects/{project}/tasks/create', [TaskController::class,'create'])->name('tasks.create');
    Route::post('/projects/{project}/tasks', [TaskController::class,'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class,'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [TaskController::class,'start'])->name('tasks.start');
    Route::post('/tasks/{task}/comments', [TaskController::class,'comment'])->name('tasks.comments.store');
    Route::post('/tasks/{task}/subtasks', [SubtaskController::class,'store'])->name('tasks.subtasks.store');
    Route::post('/subtasks/{subtask}/toggle', [SubtaskController::class,'toggle'])->name('subtasks.toggle');
    Route::post('/tasks/{task}/submit', [TaskSubmissionController::class,'store'])->name('tasks.submit');

    Route::get('/reviews', [ReviewController::class,'index'])->name('reviews.index');
    Route::post('/tasks/{task}/approve', [ReviewController::class,'approve'])->name('tasks.approve');
    Route::post('/tasks/{task}/revision', [ReviewController::class,'revision'])->name('tasks.revision');

    Route::patch('/revision-items/{item}', [RevisionController::class,'updateItem'])->name('revision-items.update');
    Route::post('/revisions/{revision}/resubmit', [RevisionController::class,'resubmit'])->name('revisions.resubmit');

    Route::get('/people', [PeopleController::class,'index'])->name('people.index');
    Route::get('/people/{membership}/edit', [WorkspaceMemberController::class,'edit'])->name('people.edit');
    Route::put('/people/{membership}', [WorkspaceMemberController::class,'update'])->name('people.update');
    Route::post('/people/{membership}/revoke', [WorkspaceMemberController::class,'revoke'])->name('people.revoke');
    Route::post('/people/{membership}/reactivate', [WorkspaceMemberController::class,'reactivate'])->name('people.reactivate');
    Route::post('/invitations', [InvitationController::class,'store'])->name('invitations.store');
    Route::post('/people/internal', [InternalMemberController::class,'store'])->name('people.internal.store');

    Route::get('/reports', [ReportController::class,'index'])->name('reports.index');
    Route::get('/audit', [AuditController::class,'index'])->name('audit.index');
    Route::get('/access-log', AccessLogController::class)->name('access-log.index');
});
