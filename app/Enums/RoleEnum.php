<?php

namespace App\Enums;

enum RoleEnum: string
{
    case MEMBER = 'member';
    case ADMIN = 'admin';

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
