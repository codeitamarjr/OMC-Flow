<?php

namespace App\Livewire\Team;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Email;

class TeamManager extends Component
{
    public string $name = '';
    public string $email = '';
    public string $currentUserRole = 'member';
    public $teamMembers = [];

    public function mount()
    {
        $this->loadTeam();
    }

    /**
     * Loads the current team members and the current user's role.
     *
     * Used on mount to initially load the team members and whenever the user's role is updated.
     *
     * @return void
     */
    public function loadTeam(): void
    {
        $business = Auth::user()->currentBusiness;

        $this->teamMembers = $business->users()->withPivot('role')->get();

        $this->currentUserRole = $business->users()
            ->where('user_id', Auth::id())
            ->first()?->pivot?->role ?? 'member';
    }

    /**
     * Invite a new user to the business by email, or add an existing user.
     *
     * @return void
     */
    public function invite(): void
    {
        $this->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);

        $business = Auth::user()->currentBusiness;

        // Check if the user already exists
        $user = \App\Models\User::where('email', $this->email)->first();

        if ($user) {
            // If already on team, prevent duplicate
            if ($business->users()->where('user_id', $user->id)->exists()) {
                session()->flash('error', 'User is already part of the business.');
                return;
            }

            $this->sendInvitationOnlyEmail($user);
        } else {
            $user = \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt(Str::random(32)),
                'current_business_id' => $business->id,
            ]);

            $this->sendIntiveEmail($user);
        }

        $business->users()->attach($user->id, ['role' => 'member']);

        $this->reset(['email', 'name']);
        $this->loadTeam();
        session()->flash('success', 'User invited successfully.');
    }

    /**
     * Re-send the invitation email to the specified user.
     *
     * @param \App\Models\User $user The user to whom the invitation email is resent.
     * @return void
     */
    public function reSendInvite(\App\Models\User $user): void
    {
        $this->sendInvitationOnlyEmail($user);
        session()->flash('success', 'Invitation resent successfully.');
    }

    /**
     * Send an invitation email to the specified user with a password reset link.
     *
     * @param \App\Models\User $user The user to whom the invitation email is sent.
     * @return void
     */
    protected function sendIntiveEmail(\App\Models\User $user): void
    {
        $token = Password::createToken($user);

        $url = url(route('invite-password.set', ['token' => $token, 'email' => $user->email]));

        Mail::to($user->email)->send(new \App\Mail\InviteUserToBusinessSetPassword($user, $url));
    }

    /**
     * Send an invitation email to an existing user without a password reset link.
     *
     * This method is used when the user already exists in the system but is not yet part of the current business.
     * It sends an invitation notification email to the specified user's email address.
     *
     * @param \App\Models\User $user The user to whom the invitation email is sent.
     * @return void
     */
    protected function sendInvitationOnlyEmail(\App\Models\User $user): void
    {
        Mail::to($user->email)->send(new \App\Mail\InviteUserToBusinessNotification($user));
    }

    /**
     * Remove a user from the current business.
     *
     * @param int $userId The ID of the user to remove.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function remove(int $userId): void
    {
        $business = Auth::user()->currentBusiness;

        if (Auth::user()->roleInCurrentBusiness() !== 'admin') {
            abort(403);
        }

        if ($userId === Auth::id()) {
            session()->flash('error', 'You cannot remove yourself.');
            return;
        }

        $business->users()->detach($userId);

        $user = \App\Models\User::findOrFail($userId);
        if ($user->current_business_id === $business->id) {
            $user->update(['current_business_id' => null]);
        }

        $this->loadTeam();
        session()->flash('success', 'User removed.');
    }


    public function render()
    {
        return view('livewire.team.team-manager');
    }
}
