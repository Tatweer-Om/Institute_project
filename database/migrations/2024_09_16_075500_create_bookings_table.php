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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // Primary key
            // $table->tinyInteger('booking_type')->comment('1 for booking, 2 for reservation'); // Booking type
            $table->string('booking_no'); // Booking number
            $table->integer('customer_id'); // Foreign key to customers table
            $table->date('booking_date'); // Booking date
            $table->date('rent_date'); // Rent date
            $table->date('return_date'); // Return date
            $table->integer('duration'); // Duration in days
            $table->integer('dress_id'); // Dress name (foreign key or integer reference)
            $table->decimal('price', 8, 3); // Price with 3 digits after the decimal
            $table->decimal('discount', 8, 2)->nullable(); // Discount with 2 digits after the decimal (nullable)
            $table->decimal('total_price', 8, 3); // Total price with 3 digits after the decimal
            $table->longText('notes')->nullable(); // Notes (optional)
            $table->tinyInteger('status')->default(1)->comment('1 for new, 2 for rented, 3 for finished'); // Booking status
            $table->integer('user_id')->nullable(); // Foreign key to users table (nullable)
            $table->string('added_by')->nullable(); // User who added the booking
            $table->string('updated_by')->nullable(); // User who updated the booking
            $table->timestamps(); // created_at and up
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};