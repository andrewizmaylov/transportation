<?php

declare(strict_types=1);

namespace Src\SharedKernel\ApplicationLayer\Processes;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GetApiTokenForRegisteredUserProcess
{
    public function execute(array $params): array
    {
        $user = User::where('email', $params['email'])->first();

        if (! $user || ! Hash::check($params['password'], $user->password)) {
            return [
                'email' => $user->email,
                'token' => null,
                'status' => 401,
            ];
        }

        $token = $user->createToken('token')->plainTextToken;

        return [
            'email' => $user->email,
            'token' => $token,
            'status' => 200,
        ];
    }
}
