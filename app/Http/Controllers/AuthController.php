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

        // Create token with scopes
        $accessToken = $user->createToken('invoice-api-token', [
            'create-invoices',
            'read-invoices',
        ]);

        return response()->json([
            'user' => $user,
            'token' => $accessToken->plainTextToken,
        ]);
    }
}
