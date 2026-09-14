<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_users';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Check given raw password against stored password (supports Bcrypt, MD5, and plain text)
     */
    public function verifyPassword(string $plainPassword): bool
    {
        // 1. MD5 check (legacy CodeIgniter)
        if (md5($plainPassword) === $this->password) {
            // Automatically upgrade password to standard Bcrypt hash
            $this->password = Hash::make($plainPassword);
            $this->save();
            return true;
        }

        // 2. Plaintext match (fallback legacy)
        if ($plainPassword === $this->password) {
            $this->password = Hash::make($plainPassword);
            $this->save();
            return true;
        }

        // 3. Standard Bcrypt check
        try {
            if (Hash::check($plainPassword, $this->password)) {
                return true;
            }
        } catch (\Throwable $e) {
            // Not a valid Bcrypt hash
        }

        return false;
    }
}
