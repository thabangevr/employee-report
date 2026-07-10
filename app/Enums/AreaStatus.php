<?php

declare(strict_types=1);

namespace App\Enums;

enum AreaStatus: string
{
    case Green = 'green';
    case Amber = 'amber';
    case Blocker = 'blocker';

    public function label(): string
    {
        return match ($this) {
            self::Green => 'Green',
            self::Amber => 'Amber',
            self::Blocker => 'Blocker',
        };
    }

    public function pillClass(): string
    {
        return match ($this) {
            self::Green => 'pill-green',
            self::Amber => 'pill-amber',
            self::Blocker => 'pill-blocker',
        };
    }
}
