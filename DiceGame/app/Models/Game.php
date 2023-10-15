<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\User;


class Game extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dice_1', 'dice_2', 'gameWon', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Roll one dice.
     * @return int random number between 1 and 6
     */
    public function rollDice(): int
    {
        return rand(1, 6);
    }
    /**
     * Check if the game is won
     * @return string (Won or Lost)
     */
    public function isGameWon(): String
    {
        return (($this->dice_1 + $this->dice_2) === 7) ? "Won" : "Lost";
    }
}
