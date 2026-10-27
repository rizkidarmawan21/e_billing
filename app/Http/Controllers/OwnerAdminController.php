<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->latest()
            ->paginate(10);

        return view('owner.admin.index', compact('admins'));
    }

    public function create()
    {
        return view('owner.admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        return redirect()
            ->route('owner.admin.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_if($user->role !== 'admin', 404);

        return view('owner.admin.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role !== 'admin', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('owner.admin.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'admin', 404);

        // Jangan sampai owner yang sedang login menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('owner.admin.index')
                ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()
            ->route('owner.admin.index')
            ->with('success', 'Admin berhasil dihapus.');
    }
}