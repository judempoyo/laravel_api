<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{

    public function redirectToProvider(string $provider)
    {
        try {
            
            return Socialite::driver($provider)->stateless()->redirect();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Provider not supported or configuration error.'], 400);
        }
    }

    public function handleProviderCallback(string $provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve user from ' . $provider . ' provider.'], 401);
        }

        $user = $this->findOrCreateUser($socialiteUser, $provider);

        $token = $user->createToken('SocialAuthToken', ['api'])->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    protected function findOrCreateUser($socialiteUser, $provider): User
    {
        $account = SocialAccount::where('provider_name', $provider)
                                ->where('provider_id', $socialiteUser->getId())
                                ->first();

        if ($account) {
            return $account->user; 
        }

        $user = User::where('email', $socialiteUser->getEmail())->first();

        if (!$user) {
            $user = DB::transaction(function () use ($socialiteUser) {
                return User::create([
                    'name' => $socialiteUser->getName() ?? 'New User',
                    'email' => $socialiteUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), 
                    'email_verified_at' => now(), 
                ]);
            });
        }

        $user->socialAccounts()->create([
            'provider_id' => $socialiteUser->getId(),
            'provider_name' => $provider,
        ]);

        return $user;
    }
}