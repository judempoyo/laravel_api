<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 *
 * @OA\Schema(
 * schema="ValidationErrorResponse",
 * title="Validation Error Response",
 * description="Réponse standard pour les erreurs de validation (code 422)",
 * @OA\Property(property="message", type="string", example="The given data was invalid."),
 * @OA\Property(property="errors", type="object", description="Détails des erreurs de validation par champ",
 * @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken.")),
 * @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password must be at least 8 characters."))
 * ),
 * )
 */

class ValidationErrorResponse {}
