<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.dashboard.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.dashboard.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'superadmin') {
            $request->merge(['role' => 'admin']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,superadmin',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama wajib diisi!',
            'username.required' => 'Username wajib diisi!',
            'username.unique' => 'Username telah terdaftar, gunakan username lain!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal 6 karakter!',
            'role.required' => 'Role wajib dipilih!',
            'is_active.required' => 'Status akun wajib dipilih!',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLog::log(
            'created',
            'user',
            'Menambahkan User Baru',
            "Menambahkan user baru: {$user->name} ({$user->username})",
            $user->id
        );

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && $user->role === 'superadmin') {
            return redirect()->route('users.index')->with('error', 'Anda tidak memiliki akses untuk mengedit akun Superadmin!');
        }

        return view('admin.dashboard.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && $user->role === 'superadmin') {
            return redirect()->route('users.index')->with('error', 'Anda tidak memiliki akses untuk mengubah akun Superadmin!');
        }

        if ($user->id !== auth()->id() && $request->filled('password')) {
            return back()->with('error', 'Anda tidak diperbolehkan mengubah password pengguna lain!')->withInput();
        }

        if (auth()->user()->role !== 'superadmin') {
            $request->merge(['role' => $user->role]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,superadmin',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama wajib diisi!',
            'username.required' => 'Username wajib diisi!',
            'username.unique' => 'Username telah terdaftar, gunakan username lain!',
            'password.min' => 'Password minimal 6 karakter!',
            'role.required' => 'Role wajib dipilih!',
            'is_active.required' => 'Status akun wajib dipilih!',
        ]);

        if ($user->role === 'superadmin' && !$validated['is_active']) {
            return back()->with('error', 'Akun Superadmin tidak dapat dinonaktifkan!')->withInput();
        }

        if ($user->id === auth()->id() && !$validated['is_active']) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!')->withInput();
        }

        if ($user->id === auth()->id() && $request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        ActivityLog::log(
            'updated',
            'user',
            'Mengubah Data User',
            "Mengubah data user: {$user->name} ({$user->username})",
            $user->id
        );

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Toggle active/inactive status of the user.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && $user->role === 'superadmin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah status akun Superadmin!');
        }

        if ($user->role === 'superadmin') {
            return back()->with('error', 'Akun Superadmin tidak dapat dinonaktifkan!');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status akun Anda sendiri!');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log(
            'updated',
            'user',
            'Mengubah Status User',
            "Status akun user {$user->name} berhasil {$statusText}",
            $user->id
        );

        return back()->with('success', "Status user {$user->name} berhasil {$statusText}!");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && $user->role === 'superadmin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus akun Superadmin!');
        }

        if ($user->role === 'superadmin') {
            return back()->with('error', 'Akun Superadmin tidak dapat dihapus!');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log(
            'deleted',
            'user',
            'Menghapus User',
            "Menghapus user: {$userName}",
            $id
        );

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
