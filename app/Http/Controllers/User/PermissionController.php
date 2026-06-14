<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        return view('backend.pages.permissions.index', [
            'modules' => PermissionRegistry::modules(),
            'permissions' => PermissionRegistry::permissions(),
        ]);
    }
}
