<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Roles live in the role_user pivot, not a role_id column, so role_id is
     * deliberately absent here. Assign them through $user->roles()->sync().
     */
    protected $fillable = ['name', 'email', 'password', 'is_active'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function hasRole($role)
    {
        foreach ((array) $role as $single) {
            if ($this->roles()->where('slug', $single)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isEditor()
    {
        return $this->hasRole('editor');
    }

    public function assignRole($role)
    {
        $role = $role instanceof Role ? $role : Role::where('slug', $role)->firstOrFail();

        $this->roles()->syncWithoutDetaching([$role->id]);
    }
}
