<?php

namespace App\Enums;

enum RoleName: string
{
    case Administrator = 'Administrator';
    case OperatorLapangan = 'Operator Lapangan';
    case Verifikator = 'Verifikator';
    case Pimpinan = 'Pimpinan';
    case Publik = 'Publik';
}
