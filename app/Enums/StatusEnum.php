<?php

namespace App\Enums;

enum StatusEnum: string
{
    case MENUNGGU_PERSETUJUAN = 'MENUNGGU PERSETUJUAN';
    case DISETUJUI = 'DISETUJUI';
}