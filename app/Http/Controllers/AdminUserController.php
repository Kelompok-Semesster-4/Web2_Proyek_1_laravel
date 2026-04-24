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
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,mahasiswa',
            'prodi' => 'nullable|string|max:100',
        ], [
            'username.unique' => 'Username sudah digunakan!',
        ]);

        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password), // Stored as bcrypt
            'role' => $request->role,
            'prodi' => $request->role === 'mahasiswa' ? $request->prodi : null,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'add');
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'id' => 'required|exists:users,id',
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,mahasiswa',
            'prodi' => 'nullable|string|max:100',
        ], [
            'username.unique' => 'Username sudah digunakan oleh user lain!',
        ]);

        $user = User::findOrFail($id);
        
        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
            'prodi' => $request->role === 'mahasiswa' ? $request->prodi : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'edit');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }

        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'delete');
    }
}
