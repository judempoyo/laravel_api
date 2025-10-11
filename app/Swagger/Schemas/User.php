<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="User",
 * title="User Model",
 * description="Schéma de base de l'utilisateur",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Jean Dupont"),
 * @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * )

 */

class User {}
