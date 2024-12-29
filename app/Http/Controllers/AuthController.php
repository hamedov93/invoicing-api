<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            abort(401, 'Invalid credentials');
        }

        $user = $this->userRepository->getFirst(['email' => $data['email']]);

        if ($user->role !== 'admin') {
            abort(403, 'Forbidden');
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('invoice-api-token'),
        ]);
    }
}
