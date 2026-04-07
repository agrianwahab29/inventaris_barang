<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            // Check if user is active
            if (Auth::user()->status !== 'aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')->withErrors([
                    'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
                ])->onlyInput('username');
            }
            
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return redirect('/login')->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Admin only - Manage Users
    public function indexUsers(Request $request)
    {
        $query = User::orderBy('name');
        
        // Filter by status if provided, default to showing only active users
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show only active users
            $query->where('status', 'aktif');
        }
        
        $users = $query->paginate(10);
        return view('users.index', compact('users'));
    }

    public function createUser()
    {
        return view('users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,pengguna',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function editUser(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id . '|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id . '|max:255',
            'role' => 'required|in:admin,pengguna',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    public function bulkDeleteUsers(Request $request)
    {
        // Only admin can bulk delete users
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'Hanya admin yang dapat menghapus user');
        }

        $ids = $request->input('ids', '');
        
        // Convert string to array (comma-separated from JavaScript)
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        
        // Filter empty values and ensure integers
        $ids = array_filter(array_map('intval', $ids));
        
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu user untuk dihapus');
        }

        DB::beginTransaction();
        try {
            $users = User::whereIn('id', $ids)->get();
            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($users as $user) {
                // Cannot delete self
                if ($user->id === Auth::id()) {
                    $skippedCount++;
                    continue;
                }
                $user->delete();
                $deletedCount++;
            }

            DB::commit();
            
            $message = $deletedCount . ' user berhasil dihapus';
            if ($skippedCount > 0) {
                $message .= ' (' . $skippedCount . ' user dilewati karena tidak dapat dihapus)';
            }
            
            return redirect()->route('users.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
