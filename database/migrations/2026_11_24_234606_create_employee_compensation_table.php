<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enum\CommissionTypeEnum;
use App\Enum\PayTypeEnum;
use App\Enum\CommissionBasisEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_compensation', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->enum(
                'pay_type',
                array_column(PayTypeEnum::cases(), 'value')
            )->nullable();
            $table->decimal('basic_pay', 14, 2)->default(0)->comment('Monthly base or fixed component');
            $table->decimal('daily_rate', 14, 2)->nullable();
            $table->decimal('hourly_rate', 14, 2)->nullable();

            $table->enum(
                'commission_basis',
                array_column(CommissionBasisEnum::cases(), 'value')
            )->nullable();
            $table->enum(
                'commission_type',
                array_column(CommissionTypeEnum::cases(), 'value')
            )->nullable();
            $table->decimal('commission_rate', 8, 2)->nullable();

            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_compensation');
    }
};
