<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     title="Erreur de validation standard",
 *     description="Réponse standard pour les erreurs de validation (code 422) avec structure ApiResponse",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         example=false,
 *         description="Indique que l'action a échoué"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Les données fournies sont invalides.",
 *         description="Message général décrivant l'erreur"
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Détails des erreurs de validation par champ",
 *         @OA\Property(
 *             property="email",
 *             type="array",
 *             @OA\Items(type="string", example="L'email est déjà utilisé.")
 *         ),
 *         @OA\Property(
 *             property="password",
 *             type="array",
 *             @OA\Items(type="string", example="Le mot de passe doit contenir au moins 8 caractères.")
 *         )
 *     )
 * )
 */
class ValidationErrorResponse {}
