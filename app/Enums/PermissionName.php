<?php

namespace App\Enums;

enum PermissionName: string
{
    case ManageMasterData = 'manage master data';
    case CreateReport = 'create report';
    case ViewReport = 'view report';
    case EditReport = 'edit report';
    case VerifyReport = 'verify report';
    case ManageHandling = 'manage handling';
    case ViewDashboard = 'view dashboard';
    case ViewMaps = 'view maps';
    case ExportData = 'export data';
    case ViewAuditLog = 'view audit log';
}
