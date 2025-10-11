<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/auth/register",
     * tags={"Auth"},
     * summary="Enregistre un nouvel utilisateur",
     * description="Crée un nouvel utilisateur et lui émet un jeton (token) d'accès Passport.",
     * @OA\RequestBody(
     * required=true,
     * description="Données d'enregistrement de l'utilisateur",
     * @OA\JsonContent(
     * required={"name","email","password","password_confirmation"},
     * @OA\Property(property="name", type="string", example="Jean Dupont"),
     * @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
     * @OA\Property(property="password", type="string", format="password", minLength=8, example="secret123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", minLength=8, example="secret123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Utilisateur enregistré et jeton d'accès émis.",
     * @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Erreur de validation des données fournies.",
     * @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     * )
     * )
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token', ['read-content', 'write-content'])->accessToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

     /**
     * @OA\Post(
     * path="/api/v1/auth/login",
     * tags={"Auth"},
     * summary="Connecte un utilisateur",
     * description="Authentifie un utilisateur avec son email et mot de passe, puis émet un jeton (token) d'accès Passport.",
     * @OA\RequestBody(
     * required=true,
     * description="Identifiants de connexion",
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="secret123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Connexion réussie. Jeton d'accès émis.",
     * @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Identifiants invalides.",
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

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token', ['read-content', 'write-content'])->accessToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }
/**
     * @OA\Get(
     * path="/api/v1/auth/user",
     * tags={"Auth"},
     * summary="Récupère l'utilisateur authentifié",
     * description="Nécessite un jeton Passport valide dans l'en-tête Authorization.",
     * security={{"passport":{}}}, 
     * @OA\Response(
     * response=200,
     * description="Informations de l'utilisateur authentifié",
     * @OA\JsonContent(ref="#/components/schemas/User")
     * ),
     * @OA\Response(
     * response=401,
     * description="Non autorisé (Jeton manquant ou invalide)"
     * )
     * )
     */
       public function user(Request $request)
    {
        return response()->json($request->user());
    }

/**
     * @OA\Post(
     * path="/api/v1/auth/logout",
     * tags={"Auth"},
     * summary="Déconnecte l'utilisateur",
     * description="Révoque le jeton d'accès (token) actuel de l'utilisateur, forçant la déconnexion.",
     * security={{"passport":{}}},
     * @OA\Response(
     * response=200, 
     * description="Déconnexion réussie",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Logged out successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Non autorisé (Jeton manquant ou invalide)"
     * )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
