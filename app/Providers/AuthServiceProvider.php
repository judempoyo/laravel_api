<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
       
    ];


    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Passport::tokensCan([
            'social' => 'Authentification via fournisseur OAuth (Google, GitHub...)',
            'api' => 'Accès standard à l’API principale',
            'read-only' => 'Accès en lecture seule',
        ]);

        Passport::setDefaultScope([
            'api',
        ]);
    }
}