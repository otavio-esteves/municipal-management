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
        Schema::create('categories', function (Blueprint $row) {
            $row->id();
            $row->foreignId('secretariat_id')
                ->constrained()
                ->onDelete('cascade');
            $row->string('name');
            $row->string('slug');
            $row->text('description')->nullable();
            $row->softDeletes();
            $row->timestamps();
            $row->unique(['secretariat_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
