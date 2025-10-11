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

     /**
     * @OA\Get(
     * path="/api/v1/auth/socialite/{provider}",
     * tags={"Auth Socialite"},
     * summary="Redirection vers le fournisseur social",
     * description="Lance le processus d'authentification en redirigeant l'utilisateur vers la page d'autorisation du fournisseur (Google, GitHub, etc.).",
     * @OA\Parameter(
     * name="provider",
     * in="path",
     * required=true,
     * description="Nom du fournisseur social (ex: google, github)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=302,
     * description="Redirection vers le fournisseur OAuth."
     * ),
     * @OA\Response(
     * response=400,
     * description="Fournisseur non supporté ou erreur de configuration."
     * )
     * )
     */
    public function redirectToProvider(string $provider)
    {
        try {
            
            return Socialite::driver($provider)->stateless()->redirect();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Provider not supported or configuration error.'], 400);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/auth/socialite/{provider}/callback",
     * tags={"Auth Socialite"},
     * summary="Gestion du callback du fournisseur social",
     * description="Point de terminaison appelé par le fournisseur OAuth après l'autorisation de l'utilisateur. Crée/met à jour l'utilisateur et émet un jeton (token) Passport.",
     * @OA\Parameter(
     * name="provider",
     * in="path",
     * required=true,
     * description="Nom du fournisseur social (ex: google, github)",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Authentification sociale réussie. Jeton d'accès émis.",
     * @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     * ),
     * @OA\Response(
     * response=401,
     * description="Échec de la récupération des données utilisateur ou annulation par l'utilisateur."
     * )
     * )
     */
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