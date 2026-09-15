<?php
namespace App\Http\Middleware;
use App\Services\WorkspaceContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveWorkspace
{
    public function __construct(private WorkspaceContext $context) {}
    public function handle(Request $request, Closure $next): Response
    {
        $workspace = $this->context->current($request);
        abort_unless($workspace, 403, 'No active workspace membership was found.');
        $request->attributes->set('workspace', $workspace);
        View::share('currentWorkspace', $workspace);
        View::share('workspaceMembership', $this->context->membership($request->user(), $workspace));
        return $next($request);
    }
}
