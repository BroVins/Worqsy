<?php
namespace App\Http\Controllers;

use App\Enums\OrganizationRole;
use App\Models\DirectConversation;
use App\Models\DirectMessage;
use App\Models\User;
use App\Models\WorkspaceMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    private function ensureInternal(Request $request): void
    {
        $workspace=$request->attributes->get('workspace');
        $wm=$request->user()->workspaceMemberships()->where('workspace_id',$workspace->id)->firstOrFail();
        abort_if($wm->organization_role===OrganizationRole::GUEST,403,'Guest cannot use internal direct messages.');
    }

    public function index(Request $request): View
    {
        $this->ensureInternal($request);
        $workspace=$request->attributes->get('workspace');
        $conversations=DirectConversation::where('workspace_id',$workspace->id)
            ->whereHas('members',fn($q)=>$q->where('users.id',$request->user()->id))
            ->with(['members','messages'=>fn($q)=>$q->latest()->limit(1)])
            ->latest()->get();
        $people=WorkspaceMembership::where('workspace_id',$workspace->id)->where('status','ACTIVE')
            ->where('organization_role','!=','GUEST')->where('user_id','!=',$request->user()->id)->with('user')->get()->pluck('user')->filter();
        return view('messages.index',compact('conversations','people'));
    }

    public function show(Request $request, DirectConversation $conversation): View
    {
        $this->ensureInternal($request);
        abort_unless($conversation->workspace_id === $request->attributes->get('workspace')->id,404);
        abort_unless($conversation->members()->where('users.id',$request->user()->id)->exists(),403);
        $conversation->load(['members','messages.sender']);
        return view('messages.show',compact('conversation'));
    }

    public function start(Request $request): RedirectResponse
    {
        $this->ensureInternal($request);
        $workspace=$request->attributes->get('workspace');
        $data=$request->validate(['user_id'=>['required','uuid','exists:users,id']]);
        $target=User::findOrFail($data['user_id']);
        $targetMembership=WorkspaceMembership::where('workspace_id',$workspace->id)->where('user_id',$target->id)->where('organization_role','!=','GUEST')->where('status','ACTIVE')->exists();
        abort_unless($targetMembership,422,'Target must be an internal workspace member.');

        $conversation=DirectConversation::create(['workspace_id'=>$workspace->id,'created_by'=>$request->user()->id]);
        $conversation->members()->sync([$request->user()->id,$target->id]);
        return redirect()->route('messages.show',$conversation);
    }

    public function send(Request $request, DirectConversation $conversation): RedirectResponse
    {
        $this->ensureInternal($request);
        abort_unless($conversation->workspace_id === $request->attributes->get('workspace')->id,404);
        abort_unless($conversation->members()->where('users.id',$request->user()->id)->exists(),403);
        $data=$request->validate(['body'=>['required','string','max:5000']]);
        DirectMessage::create(['conversation_id'=>$conversation->id,'sender_id'=>$request->user()->id,'body'=>$data['body']]);
        return back();
    }
}
