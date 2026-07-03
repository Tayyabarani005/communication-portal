<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkspaceRole: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';
}
