<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $users = User::all();

        return $this->successResponse($users, 'Daftar user berhasil diambil');
    }
}
