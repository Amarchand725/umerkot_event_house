<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DocumentNumberService
{
    private const PREFIXES = [
        'event'   => 'EVT',
        'order'   => 'ORD',
        'invoice' => 'INV',
        'payment' => 'PAY',
    ];

    public function generate(string $type): string
    {
        if (! isset(self::PREFIXES[$type])) {
            throw new InvalidArgumentException(
                "Unsupported document type: {$type}"
            );
        }

        $year = (int) now()->year;

        $number = DB::transaction(function () use ($type, $year) {
            DB::table('document_number_counters')->insertOrIgnore([
                'document_type' => $type,
                'year' => $year,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $counter = DB::table('document_number_counters')
                ->where('document_type', $type)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            $nextNumber = $counter->last_number + 1;

            DB::table('document_number_counters')
                ->where('id', $counter->id)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return $nextNumber;
        });

        return sprintf(
            '%s-%d-%04d',
            self::PREFIXES[$type],
            $year,
            $number
        );
    }
}