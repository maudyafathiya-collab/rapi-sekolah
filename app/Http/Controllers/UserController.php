<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Room;
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
        'email' => 'nullable|email|max:255|unique:users,email',
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
        'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
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

    // Tidak boleh menghapus akun sendiri
    if ((int) $user->id === (int) Auth::id()) {
        return redirect()
            ->route('users.index')
            ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
    }

    // Admin dan Waka tidak boleh dihapus
    if (in_array((int) $user->role_id, [1, 2], true)) {
        return redirect()
            ->route('users.index')
            ->with('error', 'Akun Admin dan Waka tidak dapat dihapus.');
    }

    // PIC tidak boleh dihapus jika masih bertanggung jawab atas ruangan
    if ((int) $user->role_id === 5) {
        $hasRooms = Room::where('pic_id', $user->id)->exists();

        if ($hasRooms) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Akun PIC tidak dapat dihapus karena masih bertanggung jawab atas satu atau lebih ruangan. Pindahkan tanggung jawab ruangannya terlebih dahulu.');
        }
    }

    $user->delete();

    return redirect()
        ->route('users.index')
        ->with('success', 'Pengguna berhasil dihapus.');
}

}
