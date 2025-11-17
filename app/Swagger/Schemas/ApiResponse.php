<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Opération réussie"),
 *     @OA\Property(property="data", type="object", nullable=true),
 *     @OA\Property(property="errors", type="object", nullable=true),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         nullable=true,
 *         description="Informations de pagination si disponible",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="total", type="integer", example=150),
 *         @OA\Property(property="last_page", type="integer", example=10)
 *     )
 * )
 */
class ApiResponse {}
