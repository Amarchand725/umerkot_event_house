<?php

namespace App\Enum;

enum PayTypeEnum: string
{
    case MONTHLY = 'monthly';
    case DAILY = 'daily';
    case HOURLY = 'hourly';
    case COMMISSION = 'commission';
    case HYBRID = 'hybrid';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly Fixed',
            self::DAILY => 'Daily Wage',
            self::HOURLY => 'Hourly Wage',
            self::COMMISSION => 'Commission Based',
            self::HYBRID => 'Fixed + Commission',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn ($case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ], self::cases()),
            'label',
            'value'
        );
    }
}
