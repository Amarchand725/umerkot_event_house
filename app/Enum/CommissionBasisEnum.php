<?php

namespace App\Enum;

enum CommissionBasisEnum: string
{
    case RECEIVED_AMOUNT = 'received_amount';
    case BOOKING_AMOUNT = 'booking_amount';
    case PROFIT = 'profit';
    case FIXED_PER_EVENT = 'fixed_per_event';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED_AMOUNT => 'Received Amount',
            self::BOOKING_AMOUNT => 'Booking Amount',
            self::PROFIT => 'Profit',
            self::FIXED_PER_EVENT => 'Fixed Per Event',
        };
    }
}
