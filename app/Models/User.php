<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * @package App\Models
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        'name'              => 'string',
        'email'             => 'string',
        'role'              => 'string',
    ];

    /**
     * Validation rules — referensi/baseline.
     *
     * NOTE: rules ini sebagai dokumentasi pola — validasi sesungguhnya pakai
     * CreateUserRequest dan UpdateUserRequest karena perlu Rule::unique
     * dynamic (exclude soft-deleted + ignore self saat update).
     *
     * @var array
     */
    public static $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:6|max:255',
        'role'     => 'required|in:admin,user',
    ];

    /**
     * Daftar role yang valid.
     */
    public const ROLES = ['admin', 'user'];

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
