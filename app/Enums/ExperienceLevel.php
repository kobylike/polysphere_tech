<?php

namespace App\Enums;

enum ExperienceLevel: string
{
    case EntryLevel = 'entry';
    case Mid = 'mid';
    case Senior = 'senior';
    case Lead = 'lead';
    case Executive = 'executive';

    public function label(): string
    {
        return match ($this) {
            self::EntryLevel => 'Entry Level',
            self::Mid => 'Mid Level',
            self::Senior => 'Senior',
            self::Lead => 'Lead',
            self::Executive => 'Executive',
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
