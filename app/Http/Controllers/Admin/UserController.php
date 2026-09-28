<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', ['roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ]);

        // "password" is cast to "hashed", so store the plain value and let
        // Eloquent do the hashing exactly once.
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => true,
        ]);

        $user->roles()->sync($data['roles'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created.');
    }

    public function show(User $user)
    {
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('roles');

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            // Empty means "leave the current password alone".
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ]);

        $roleIds = $data['roles'] ?? [];
        $adminRole = Role::where('slug', 'admin')->first();
        $losingAdmin = $adminRole
            && $user->roles->contains('id', $adminRole->id)
            && ! in_array($adminRole->id, array_map('intval', $roleIds), true);

        // Guard against locking yourself out of the back office.
        if ($user->is(auth()->user()) && $losingAdmin) {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        if ($losingAdmin && $this->adminCount() <= 1) {
            return back()->with('error', 'The last admin cannot lose the admin role.');
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (filled($data['password'] ?? null)) {
            $user->password = $data['password'];
        }

        $user->save();
        $user->roles()->sync($roleIds);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && $this->adminCount() <= 1) {
            return back()->with('error', 'The last admin cannot be deleted.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted.');
    }

    public function toggle(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'User status updated.');
    }

    /**
     * Number of users that currently hold the admin role.
     */
    private function adminCount(): int
    {
        $adminRole = Role::where('slug', 'admin')->first();

        return $adminRole ? $adminRole->users()->count() : 0;
    }
}
