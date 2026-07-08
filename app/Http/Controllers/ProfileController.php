<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\CloudinaryImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Load workspaces the user belongs to with their admin status
        $myWorkspaces = $user->workspaces()->with('workspaceMembers')->get();

        // For each workspace, check if user is admin
        $myWorkspacesWithRole = $myWorkspaces->map(function ($ws) use ($user) {
            $member = WorkspaceMember::where('workspace_id', $ws->workspace_id)
                ->where('user_id', $user->user_id)
                ->first();
            $ws->my_role = $member?->role?->value ?? 'member';
            return $ws;
        });

        return view('profile.edit', [
            'user'       => $user,
            'workspaces' => $myWorkspacesWithRole,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'       => ['sometimes', 'nullable', 'string', 'max:255'],
            'username'   => ['sometimes', 'nullable', 'string', 'max:50', 'alpha_dash',
                             \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->user_id, 'user_id')],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255',
                             \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'avatar'     => ['sometimes', 'nullable', 'image', 'max:2048'], // file upload
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:500'],    // URL fallback
        ]);

        // Handle avatar file upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Delete old avatar if it was uploaded (not a URL)
            if ($user->avatar_url && str_starts_with($user->avatar_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }
            try {
                $cloudinary = app(CloudinaryImageService::class);
                if ($cloudinary->isConfigured()) {
                    $validated['avatar_url'] = $cloudinary->upload($request->file('avatar'), 'avatars');
                } else {
                    $path = $request->file('avatar')->store('avatars', 'public');
                    $validated['avatar_url'] = Storage::disk('public')->url($path);
                }
            } catch (\Throwable $e) {
                report($e);

                return back()
                    ->withInput()
                    ->withErrors(['avatar' => 'Image upload failed. Please check the Cloudinary configuration and try again.']);
            }
        }

        // Remove the 'avatar' key since it's not a db column
        unset($validated['avatar']);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
