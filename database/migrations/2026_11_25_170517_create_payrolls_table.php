<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('employee_compensation_id')->nullable()->constrained('employee_compensation')->nullOnDelete();

            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            $table->date('period_start');
            $table->date('period_end');

            $table->json('compensation_snapshot')->nullable()->comment('Snapshot of compensation at calculation time.');

            $table->decimal('gross_earnings', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('net_payable', 14, 2)->default(0);

            $table->decimal('total_present_days', 7, 2)->default(0);
            $table->decimal('total_payable_days', 7, 2)->default(0);
            $table->decimal('total_working_hours', 8, 2)->default(0);
            $table->decimal('total_overtime_hours', 8, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
