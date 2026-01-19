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

    public function viewHandling(User $user, CrisisReport $crisisReport): bool
    {
        return $this->canManageHandling($user);
    }

    public function assign(User $user, CrisisReport $crisisReport): bool
    {
        return $this->canManageHandling($user);
    }

    public function updateProgress(User $user, CrisisReport $crisisReport): bool
    {
        return $this->canManageHandling($user);
    }

    public function verify(User $user, CrisisReport $crisisReport = null): bool
    {
        return $user->hasAnyRole([
            RoleName::Administrator->value,
            RoleName::OperatorLapangan->value,
            RoleName::Verifikator->value,
        ]) || $user->can(PermissionName::VerifyReport->value);
    }

    protected function canView(User $user): bool
    {
        return $user->hasRole(RoleName::Administrator->value)
            || $user->can(PermissionName::ViewReport->value)
            || $user->can(PermissionName::CreateReport->value);
    }

    protected function canManageHandling(User $user): bool
    {
        return $user->hasRole(RoleName::Administrator->value)
            || $user->can(PermissionName::ManageHandling->value);
    }
}
