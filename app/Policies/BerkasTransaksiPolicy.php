<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BerkasTransaksi;

class BerkasTransaksiPolicy
{
    public function view(User $user, BerkasTransaksi $berkas)
    {
        return true; // Anyone authenticated can view
    }
    
    public function create(User $user)
    {
        return true; // Any authenticated user can create
    }
    
    public function update(User $user, BerkasTransaksi $berkas)
    {
        return $user->isAdmin() || $user->id === $berkas->user_id;
    }
    
    public function delete(User $user, BerkasTransaksi $berkas)
    {
        return $user->isAdmin() || $user->id === $berkas->user_id;
    }
    
    public function download(User $user, BerkasTransaksi $berkas)
    {
        return $user->isAdmin() || $user->id === $berkas->user_id;
    }
}
