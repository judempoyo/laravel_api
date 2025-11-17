<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Resources\UserResource;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 * name="Auth",
 * description="Gestion de l'authentification et de la vérification d'email"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/auth/register",
     * tags={"Auth"},
     * summary="Enregistre un nouvel utilisateur",
     * description="Crée un nouvel utilisateur, lui envoie un email de vérification et émet un jeton (token) d'accès.",
     *
     * @OA\RequestBody(
     * required=true,
     * description="Données d'enregistrement de l'utilisateur",
     *
     * @OA\JsonContent(
     * required={"name","email","password","password_confirmation"},
     *
     * @OA\Property(property="name", type="string", example="Jude mpoyo"),
     * @OA\Property(property="email", type="string", format="email", example="jude@gmail.com"),
     * @OA\Property(property="password", type="string", format="password", minLength=8, example="secret123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", minLength=8, example="secret123")
     * )
     * ),
     *
     * @OA\Response(
     * response=201,
     * description="Utilisateur enregistré. Un email de vérification a été envoyé.",
     *
     * @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     * ),
     *
     * @OA\Response(
     * response=422,
     * description="Erreur de validation des données fournies.",
     *
     * @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     * )
     * )
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token', ['api'])->accessToken;


        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user),
        ], 'Utilisateur enregistré avec succès. Veuillez consulter votre boîte mail pour vérifier votre compte.', 201);
    }

    /**
     * @OA\Post(
     * path="/api/v1/auth/login",
     * tags={"Auth"},
     * summary="Connecte un utilisateur",
     * description="Authentifie un utilisateur avec son email et mot de passe, puis émet un jeton (token) d'accès Passport. L'email doit être vérifié.",
     *
     * @OA\RequestBody(
     * required=true,
     * description="Identifiants de connexion",
     *
     * @OA\JsonContent(
     * required={"email","password"},
     *
     * @OA\Property(property="email", type="string", format="email", example="jude@gmail.com"),
     * @OA\Property(property="password", type="string", format="password", example="secret123")
     * )
     * ),
     *
     * @OA\Response(
     * response=200,
     * description="Connexion réussie. Jeton d'accès émis.",
     *
     * @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     * ),
     *
     * @OA\Response(
     * response=403,
     * description="L'email n'est pas vérifié.",
     * ),
     * @OA\Response(
     * response=422,
     * description="Identifiants invalides.",
     *
     * @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     * )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return ApiResponse::error('Identifiants incorrects', ['email' => ['Identifiants incorrects']], 422);
        }

        $token = $user->createToken('auth_token', ['api'])->accessToken;

        if (! $user->hasVerifiedEmail()) {
            ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user),
        ], 'Connexion réussie - Votre adresse email n\'est pas vérifiée.');
        }


        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user),
        ], 'Connexion réussie');
    }

    /**
     * @OA\Get(
     * path="/api/v1/auth/email/verify/{id}/{hash}",
     * tags={"Auth"},
     * summary="Vérifie l'adresse email de l'utilisateur",
     * description="Marque l'email de l'utilisateur authentifié comme vérifié.",
     *
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID de l'utilisateur",
     *
     * @OA\Schema(type="integer")
     * ),
     *
     * @OA\Parameter(
     * name="hash",
     * in="path",
     * required=true,
     * description="Hash de vérification",
     *
     * @OA\Schema(type="string")
     * ),
     *
     * @OA\Response(
     * response=200,
     * description="Email vérifié avec succès."
     * ),
     * @OA\Response(
     * response=400,
     * description="Le lien de vérification est invalide ou l'email est déjà vérifié."
     * )
     * )
     */
    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (! $user) {
            return ApiResponse::error('Utilisateur non trouvé.', null, 404);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return ApiResponse::error('Lien de vérification invalide.', null, 400);
        }

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'L\'adresse email a déjà été vérifiée.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        //return ApiResponse::success(null, 'Adresse email vérifiée avec succès.');
        return redirect()->to(config('app.frontend_url') . '/email-verified');
    }

    /**
     * @OA\Post(
     * path="/api/v1/auth/email/verification-notification",
     * tags={"Auth"},
     * summary="Renvoyer l'email de vérification",
     * description="Envoie un nouvel email de vérification à l'utilisateur authentifié si son email n'est pas encore vérifié.",
     * security={{"passport":{}}},
     *
     * @OA\Response(
     * response=200,
     * description="Email de vérification renvoyé."
     * ),
     * @OA\Response(
     * response=400,
     * description="L'email est déjà vérifié."
     * ),
     * @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::error('Votre adresse email est déjà vérifiée.', null, 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return ApiResponse::success(null, 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
    }

    /**
     * @OA\Get(
     * path="/api/v1/auth/user",
     * tags={"Auth"},
     * summary="Récupère l'utilisateur authentifié",
     * security={{"passport":{}}},
     *
     * @OA\Response(
     * response=200,
     * description="Utilisateur récupéré",
     *
     * @OA\JsonContent(
     *
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Utilisateur authentifié"),
     * @OA\Property(property="data", type="object", ref="#/components/schemas/UserResource"),
     * @OA\Property(property="errors", type="object", nullable=true, example=null),
     * @OA\Property(property="meta", type="object", nullable=true, example=null)
     * )
     * ),
     *
     * @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return ApiResponse::success(UserResource::make($user), 'Utilisateur authentifié');
    }

    /**
     * @OA\Post(
     * path="/api/v1/auth/logout",
     * tags={"Auth"},
     * summary="Déconnecte l'utilisateur",
     * security={{"passport":{}}},
     *
     * @OA\Response(
     * response=200,
     * description="Déconnexion réussie",
     *
     * @OA\JsonContent(
     *
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Déconnexion réussie"),
     * @OA\Property(property="data", type="object", nullable=true, example=null),
     * @OA\Property(property="errors", type="object", nullable=true, example=null),
     * @OA\Property(property="meta", type="object", nullable=true, example=null)
     * )
     * ),
     *
     * @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return ApiResponse::success(null, 'Déconnexion réussie');
    }

    /**
     * @OA\Post(
     * path="/api/v1/auth/refresh",
     * tags={"Auth"},
     * summary="Rafraîchit le jeton d'accès",
     * description="Rafraîchit le jeton d'accès de l'utilisateur authentifié.",
     * security={{"passport":{}}},
     *
     * @OA\Response(
     * response=200,
     * description="Jeton rafraîchi avec succès.",
     *
     * @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     * ),
     *
     * @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->token()->revoke();
        $newToken = $user->createToken('auth_token', ['api'])->accessToken;

        return ApiResponse::success([
            'token' => $newToken,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user),
        ], 'Jeton rafraîchi avec succès');
    }
}
