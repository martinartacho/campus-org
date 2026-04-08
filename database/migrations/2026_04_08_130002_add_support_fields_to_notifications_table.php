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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('ticket_id')->nullable()->after('content');
            $table->string('template_type')->nullable()->after('ticket_id');
            $table->boolean('is_support_ticket')->default(false)->after('template_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['ticket_id', 'template_type', 'is_support_ticket']);
        });
    }
};
