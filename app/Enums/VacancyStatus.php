<?php

namespace App\Enums;

enum VacancyStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Closed = 'closed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Closed => 'Closed',
            self::Archived => 'Archived',
        };
    }

    /**
     * Tailwind-style badge color token, useful once the UI is built.
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'green',
            self::Closed => 'amber',
            self::Archived => 'slate',
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
