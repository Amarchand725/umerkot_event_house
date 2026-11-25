<?php

namespace App\Enum;

enum CommissionTypeEnum: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage (%)',
            self::FIXED => 'Fixed Amount (PKR)',
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
