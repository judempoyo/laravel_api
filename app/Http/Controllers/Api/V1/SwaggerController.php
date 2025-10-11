<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;


/**
 * @OA\OpenApi(
 * @OA\Info(
 * title="API V1 d'Authentification (Passport/Socialite)",
 * version="1.0.0",
 * description="Documentation de l'API d'authentification et de gestion des utilisateurs.",
 * ),
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Serveur API V1"
 * ),
 * @OA\Components(
 * securitySchemes={
 * @OA\SecurityScheme(
 * securityScheme="passport",
 * type="http",
 * scheme="bearer",
 * bearerFormat="Passport Token",
 * description="Entrez votre jeton (token) Passport dans l'en-tête 'Authorization: Bearer [token]'"
 * )
 * }
 * )
 * )
 */
class SwaggerController {}
