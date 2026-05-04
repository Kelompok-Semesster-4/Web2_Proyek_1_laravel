<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.user', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,mahasiswa'],
            'prodi' => ['nullable', 'string', 'max:100'],
        ], [
            'username.unique' => 'Username sudah digunakan!',
        ]);

        User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'prodi' => $validated['role'] === 'mahasiswa' ? ($validated['prodi'] ?? null) : null,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'add');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $user->getKey()],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin,mahasiswa'],
            'prodi' => ['nullable', 'string', 'max:100'],
        ], [
            'username.unique' => 'Username sudah digunakan oleh user lain!',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'prodi' => $validated['role'] === 'mahasiswa' ? ($validated['prodi'] ?? null) : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'edit');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }

        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'delete');
    }
}
