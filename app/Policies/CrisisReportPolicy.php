<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\CrisisReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CrisisReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->canView($user);
    }

    public function view(User $user, CrisisReport $crisisReport): bool
    {
        return $this->canView($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::Administrator->value)
            || $user->can(PermissionName::CreateReport->value);
    }

    public function update(User $user, CrisisReport $crisisReport): bool
    {
        return $user->hasRole(RoleName::Administrator->value)
            || $user->can(PermissionName::EditReport->value);
    }

    public function verify(User $user, ?CrisisReport $crisisReport = null): bool
    {
        return $user->hasRole(RoleName::Administrator->value)
            || $user->can(PermissionName::VerifyReport->value);
    }

    protected function canView(User $user): bool
    {
        return $user->hasRole(RoleName::Administrator->value)
            || $user->can(PermissionName::ViewReport->value)
            || $user->can(PermissionName::CreateReport->value);
    }
}
