<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 *
 * @OA\Schema(
 * schema="AuthTokenResponse",
 * title="Auth Token Response",
 * description="Réponse standard après connexion ou enregistrement",
 * @OA\Property(property="token", type="string", description="Jeton d'accès (Access Token) Passport"),
 * @OA\Property(property="token_type", type="string", example="Bearer"),
 * @OA\Property(property="user", ref="#/components/schemas/User", description="Objet utilisateur authentifié"),
 * )
 */

class AuthTokenResponse {}
