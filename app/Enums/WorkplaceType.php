<?php

namespace App\Enums;

enum WorkplaceType: string
{
    case Onsite = 'onsite';
    case Remote = 'remote';
    case Hybrid = 'hybrid';

    public function label(): string
    {
        return match ($this) {
            self::Onsite => 'On-site',
            self::Remote => 'Remote',
            self::Hybrid => 'Hybrid',
        };
    }

    /** @return array<string, string> value => label, for select inputs */
    public static function options(): array
    {
        return array_combine(
            array_map(fn(self $c) => $c->value, self::cases()),
            array_map(fn(self $c) => $c->label(), self::cases())
        );
    }
}
