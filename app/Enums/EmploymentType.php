<?php

namespace App\Enums;

enum EmploymentType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Contract = 'contract';
    case Internship = 'internship';
    case Temporary = 'temporary';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'Full-time',
            self::PartTime => 'Part-time',
            self::Contract => 'Contract',
            self::Internship => 'Internship',
            self::Temporary => 'Temporary',
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
