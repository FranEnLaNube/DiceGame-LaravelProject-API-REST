<?php

namespace App\Providers;
use Laravel\Passport\Passport;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensCan([
            'player' => 'logout','show', 'update', 'destroy', 'store', 'showPlayerGames', 'destroyPlayerGames',
            'admin' => 'logout','index', 'ranking', 'showLoser', 'showWinner',
        ]);
        Passport::setDefaultScope([
            'player'
        ]);
    }
}
