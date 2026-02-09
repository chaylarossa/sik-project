<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Unit $unit): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Unit $unit): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $this->canManage($user);
    }

    protected function canManage(User $user): bool
    {
        return $user->hasRole(RoleName::Administrator->value)
            || $user->can(PermissionName::ManageMasterData->value);
    }
}
