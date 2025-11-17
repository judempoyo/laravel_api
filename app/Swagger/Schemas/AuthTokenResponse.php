<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AuthTokenResponse",
 *     title="Réponse API standard pour Auth",
 *     description="Réponse standard après connexion ou enregistrement, avec structure ApiResponse",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=true,
 *         description="Indique si l'action a réussi"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Connexion réussie",
 *         description="Message lisible pour l'utilisateur"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         description="Données principales de la réponse",
 *         @OA\Property(property="token", type="string", description="Jeton d'accès (Access Token) Passport"),
 *         @OA\Property(property="token_type", type="string", example="Bearer"),
 *         @OA\Property(property="user", ref="#/components/schemas/UserResource", description="Objet utilisateur authentifié")
 *     )
 * )
 */
class AuthTokenResponse {}
