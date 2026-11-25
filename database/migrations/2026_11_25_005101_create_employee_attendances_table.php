<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->decimal('payable_units', 5, 2)->default(1)->comment('1 = full day, 0.5 = half day, 0 = unpaid');
            $table->decimal('worked_hours', 6, 2)->default(0);
            $table->decimal('overtime_hours', 6, 2)->default(0);

            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
