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
        Passport::tokensCan([
            'read-content' => 'Read project contents',
            'write-content' => 'Create or update project contents',
        ]);


        Passport::defaultScopes([
            'read-content',
        ]);
    }
}