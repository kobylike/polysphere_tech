<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case New           = 'new';
    case Reviewing     = 'reviewing';
    case Shortlisted   = 'shortlisted';
    case Interviewing  = 'interviewing';
    case Offer         = 'offer';
    case Hired         = 'hired';
    case Rejected      = 'rejected';
    case Withdrawn     = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::New          => 'New',
            self::Reviewing    => 'Reviewing',
            self::Shortlisted  => 'Shortlisted',
            self::Interviewing => 'Interviewing',
            self::Offer        => 'Offer sent',
            self::Hired        => 'Hired',
            self::Rejected     => 'Not proceeding',
            self::Withdrawn    => 'Withdrawn',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New          => 'primary',
            self::Reviewing    => 'info',
            self::Shortlisted  => 'warning',
            self::Interviewing => 'warning',
            self::Offer        => 'success',
            self::Hired        => 'success',
            self::Rejected     => 'danger',
            self::Withdrawn    => 'secondary',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Hired, self::Rejected, self::Withdrawn], true);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return array_combine(
            array_map(fn(self $c) => $c->value, self::cases()),
            array_map(fn(self $c) => $c->label(), self::cases())
        );
    }
}
