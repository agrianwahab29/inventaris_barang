<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Include status check in credentials so Login event doesn't fire for inactive users
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'status'   => 'aktif',
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // Check if user exists but is inactive (for better error message)
        $user = User::where('username', $credentials['username'])->first();
        if ($user && $user->status !== 'aktif') {
            return redirect('/login')->withErrors([
                'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ])->onlyInput('username');
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
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } elseif (!$request->filled('status')) {
            // Default: show only active users
            $query->where('status', 'aktif');
        }
        // 'status=all' → no filter, shows all
        
        $users = $query->paginate(10);
        
        // Compute stats from FULL dataset, not paginated collection
        $stats = [
            'total'    => User::count(),
            'aktif'    => User::where('status', 'aktif')->count(),
            'admin'    => User::where('role', 'admin')->count(),
            'pengguna' => User::where('role', 'pengguna')->count(),
        ];
        
        return view('users.index', compact('users', 'stats'));
    }

    public function createUser()
    {
        return view('users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'email'    => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8|max:255|confirmed',
            'role'     => 'required|in:admin,pengguna',
            'status'   => 'required|in:aktif,nonaktif',
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
            'name'     => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|unique:users,username,' . $user->id . '|regex:/^[a-zA-Z0-9_]+$/',
            'email'    => 'required|email|unique:users,email,' . $user->id . '|max:255',
            'password' => 'nullable|string|min:8|max:255',
            'role'     => 'required|in:admin,pengguna',
            'status'   => 'required|in:aktif,nonaktif',
        ]);

        // C-1: Prevent admin from deactivating or demoting themselves
        if ($user->id === Auth::id()) {
            if ($validated['status'] === 'nonaktif') {
                return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.')->withInput();
            }
            if ($validated['role'] !== 'admin') {
                return back()->with('error', 'Anda tidak dapat menurunkan role akun sendiri.')->withInput();
            }
        }

        // C-2: Prevent demoting/deactivating the last admin
        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat menurunkan role admin terakhir. Minimal harus ada 1 admin dalam sistem.')->withInput();
            }
        }

        // Handle password - only update if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // H-3: Invalidate sessions of deactivated users
        if ($validated['status'] === 'nonaktif' && $user->id !== Auth::id()) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        // C-2: Prevent deleting the last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus admin terakhir. Minimal harus ada 1 admin dalam sistem.');
            }
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    public function bulkDeleteUsers(Request $request)
    {
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

            // Count remaining admins NOT in the delete list
            $remainingAdmins = User::where('role', 'admin')
                ->whereNotIn('id', $ids)
                ->count();

            foreach ($users as $user) {
                // Cannot delete self
                if ($user->id === Auth::id()) {
                    $skippedCount++;
                    continue;
                }

                // C-2: Cannot delete the last admin
                if ($user->role === 'admin' && $remainingAdmins < 1) {
                    $skippedCount++;
                    continue;
                }

                // Decrement remaining admin count if this admin is being deleted
                if ($user->role === 'admin') {
                    $remainingAdmins--;
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
            // H-4: Log real error, show generic message to user
            Log::error('Bulk delete users failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'admin_id' => Auth::id(),
            ]);
            return back()->with('error', 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.');
        }
    }
}
