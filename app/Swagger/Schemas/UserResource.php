<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *
 * schema="UserResource",
 *
 * title="Ressource Utilisateur",
 *
 * description="Représentation d'un utilisateur, incluant potentiellement son profil.",
 *
 * @OA\Property(property="id", type="integer", example=1, description="ID unique de l'utilisateur"),
 * @OA\Property(property="name", type="string", example="Jude mpoyo", description="Nom complet de l'utilisateur"),
 * @OA\Property(property="email", type="string", format="email", example="jude@gmail.com", description="Adresse e-mail"),
 * @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, description="Date de vérification de l'e-mail"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Date de création du compte"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Date de dernière mise à jour du compte"),
 * @OA\Property(property="profile", ref="#/components/schemas/ProfileResource", nullable=true, description="Ressource du profil détaillé, si chargé"),
 *
 * )
 */
class UserResource {}
