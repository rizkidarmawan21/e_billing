<?php

namespace App\Http\Controllers;

use App\Models\User;

class OwnerAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->latest()
            ->paginate(10);

        return view('owner.admin.index', compact('admins'));
    }
}