<?php

namespace SuStartX\JWTRedisMultiAuth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles as OriginalHasRole;

class JWTRedisMultiAuthAuthenticatableBaseModel extends BaseModel
{
    protected static $redis_observer = null;

    use HasApiTokens, HasFactory, Notifiable;

    use OriginalHasRole {
        assignRole as protected originalAssignRole;
        givePermissionTo as protected originalGivePermissionTo;
    }

    public function assignRole(...$roles)
    {
        $this->originalAssignRole(...$roles);

        $this->triggerTheObserver();

        return $this;
    }

    public function givePermissionTo(...$permissions)
    {
        $this->originalGivePermissionTo(...$permissions);

        $this->triggerTheObserver();

        return $this;
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    public function hasDirectPermission($permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }

        if (is_int($permission)) {
            return $this->permissions->contains('id', $permission);
        }

        return false;
    }

    public function hasPermissionViaRole($permission): bool
    {
        $roles = $this->roles;

        if (is_string($permission)) {
            foreach ($roles as $role) {
                if ($role->permissions->contains('name', $permission)) {
                    return true;
                }
            }
        }

        if (is_int($permission)) {
            foreach ($roles as $role) {
                if ($role->permissions->contains('id', $permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getRedisKey()
    {
        $class = get_class($this);
        $class = explode('\\', $class);
        $class = end($class);
        $class = strtolower($class);

        return config('jwtredismultiauth.redis_auth_prefix') . $class . '_' . $this->getJWTIdentifier();
    }

    public function triggerTheObserver()
    {
        $model = $this;

        $class = config('jwtredismultiauth.observer');

        (new $class())->updated($model);
    }

//    public static function boot()
//    {
//        static::observe(self::$redis_observer);
//    }
}
