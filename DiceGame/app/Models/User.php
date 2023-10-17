<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Game;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nickname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function games()
    {
        return $this->hasMany(Game::class);
    }
    /**
     * Calculate success rate of a specific player
     * @return float
     */
    public function calculatePlayerSuccessRate(): float
    {
        // Get number of games won by this player
        $userGamesWon = $this->games->where('gameWon', 'Won')->count();
        // Get number of games played by this player
        $userGames = $this->games->count();
        if ($userGames == 0) {
            return 0.0;
        }

        $successRate = ($userGamesWon / $userGames) * 100;
        $successRate = number_format($successRate, 2);

        return $successRate;
    }
}
