<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // For guest users
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // For logged users
            $table->enum('status', ['active', 'abandoned', 'converted'])->default('active');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['session_id']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['expires_at']);
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained('campus_courses')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price_at_time', 10, 2); // Store price when added to cart
            $table->json('course_snapshot')->nullable(); // Store course data snapshot
            $table->timestamps();
            
            // Indexes
            $table->index(['cart_id', 'course_id']);
            $table->unique(['cart_id', 'course_id']); // One course per cart
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
