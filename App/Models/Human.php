<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Human extends Authenticatable
{
    /**
 * @OA\Schema(
 *     schema="Human",
 *     type="object",
 *     title="Human Model",
 *     required={"human_id", "age", "name", "password", "email"},
 *     @OA\Property(property="human_id", type="integer", example=123),
 *     @OA\Property(property="age", type="integer", example=30),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="password", type="string", example="$2y$12$..."),
 *     @OA\Property(property="location", type="string", nullable=true, example="New York"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="creditcard", type="string", nullable=true, example="1234-5678-9012-3456")
 * )
 */
    use HasApiTokens, Notifiable;
    public $timestamps = false;
    protected $table = 'human';
    protected $fillable = ['human_id', 'age', 'name', 'password', 'location', 'email', 'creditcard'];
    protected $primaryKey = 'human_id';


    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function setPasswordAttribute($value)
{
    $this->attributes['password'] = bcrypt($value);
}
}

