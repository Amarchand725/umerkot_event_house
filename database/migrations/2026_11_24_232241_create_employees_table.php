<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('referred_by_id')->nullable()->constrained('refrences')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('avatar_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignId('cnic_front_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignId('cnic_back_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->date('joining_date');
            $table->date('leaving_date')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('caste')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
