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
        Schema::create('winn_losses', function (Blueprint $table) {
            $table->id();
            $table->string('total_trade')->nullable(); // Customer name
            $table->string('win')->nullable(); // Customer name
            $table->string('loss')->nullable(); // Customer email (unique)
            $table->string('percentage_win')->nullable(); // Phone number
            $table->string('percentage_los')->nullable(); // Phone number
            $table->longText('notes')->nullable(); // Address (optional)
            $table->integer('user_id')->nullable(); // Foreign key to users table (nullable)
            $table->string('added_by')->nullable(); // User who added the booking
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('winn_losses');
    }
};
