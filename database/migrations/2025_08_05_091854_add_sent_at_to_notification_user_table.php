<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('notification_user', function (Blueprint $table) {
            // Agregar timestamps para todos los canales
            $table->boolean('email_sent')->default(false)->after('push_sent');
            $table->timestamp('email_sent_at')->nullable()->after('email_sent');
            $table->boolean('web_sent')->default(false)->after('email_sent_at');
            $table->timestamp('web_sent_at')->nullable()->after('web_sent');
            $table->timestamp('push_sent_at')->nullable()->after('push_sent');        });
    }

    public function down()
    {
        Schema::table('notification_user', function (Blueprint $table) {
            $table->dropColumn([
                'email_sent', 
                'email_sent_at', 
                'web_sent', 
                'web_sent_at',
                'push_sent_at'            
            ]);
        });
    }
};