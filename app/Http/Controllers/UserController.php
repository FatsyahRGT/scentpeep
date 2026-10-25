<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::orderBy('name');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $user->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function toggle(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan saat ini!');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('users.index')
            ->with('success', "Akun {$user->name} berhasil {$statusText}!");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update([
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Akun reseller {$user->name} berhasil disetujui dan diaktifkan!");
    }

    public function reject(User $user): RedirectResponse
    {
        $user->update([
            'status' => User::STATUS_REJECTED,
            'is_active' => false,
        ]);

        return redirect()->route('users.index')
            ->with('warning', "Pendaftaran reseller {$user->name} telah ditolak.");
    }
}
