<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case CEO = 'ceo';
    case Manager = 'manager';
    case Employee = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::CEO => 'CEO',
            self::Manager => 'Manager',
            self::Employee => 'Employee',
        };
    }
}
