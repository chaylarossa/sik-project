<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        foreach (PermissionName::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => $guard,
            ]);
        }

        $rolePermissions = [
            [
                'role' => RoleName::Administrator,
                'permissions' => PermissionName::cases(),
            ],
            [
                'role' => RoleName::OperatorLapangan,
                'permissions' => [
                    PermissionName::CreateReport,
                    PermissionName::ViewReport,
                    PermissionName::EditReport,
                    PermissionName::ManageHandling,
                ],
            ],
            [
                'role' => RoleName::Verifikator,
                'permissions' => [
                    PermissionName::ViewReport,
                    PermissionName::ViewDashboard,
                    PermissionName::VerifyReport,
                ],
            ],
            [
                'role' => RoleName::Pimpinan,
                'permissions' => [
                    PermissionName::ViewReport,
                    PermissionName::ViewDashboard,
                    PermissionName::ExportData,
                    PermissionName::ViewAuditLog,
                ],
            ],
            [
                'role' => RoleName::Publik,
                'permissions' => [
                    PermissionName::ViewReport,
                ],
            ],
        ];

        foreach ($rolePermissions as $roleConfig) {
            /** @var RoleName $role */
            $role = $roleConfig['role'];
            $permissions = $roleConfig['permissions'];

            $roleModel = Role::firstOrCreate([
                'name' => $role->value,
                'guard_name' => $guard,
            ]);

            $permissionValues = collect($permissions)
                ->map(static fn (PermissionName $permission) => $permission->value)
                ->all();

            $roleModel->syncPermissions($permissionValues);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
