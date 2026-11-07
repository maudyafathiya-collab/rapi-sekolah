<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
    /**
     * Admin (role_id = 1) dan Waka (role_id = 2)
     * memiliki hak yang sama untuk mengelola pengguna.
     */
    private function authorizeAdmin(): void
    {
        $roleId = (int) Auth::user()->role_id;

        if (!in_array($roleId, [1, 2], true)) {
            abort(403, 'Hanya Admin dan Waka yang boleh mengelola pengguna.');
        }
    }


    public function index()
    {
        $this->authorizeAdmin();

        $users = User::with('role')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'fullname' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:6',
        ]);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();

        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'fullname' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if ((int) $user->id === (int) Auth::id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
