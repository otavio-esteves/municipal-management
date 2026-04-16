<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ODS-001
            $table->string('title');
            $table->string('location')->nullable();
            $table->text('observation')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');

            $table->foreignId('secretariat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
