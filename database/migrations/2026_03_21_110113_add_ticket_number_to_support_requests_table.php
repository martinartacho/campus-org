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
        Schema::table('support_requests', function (Blueprint $table) {
            $table->string('ticket_number')->nullable()->after('status');
        });
        
        // Add unique index after filling existing records
        Schema::table('support_requests', function (Blueprint $table) {
            $table->unique('ticket_number');
        });
        
        // Generate ticket numbers for existing records
        \DB::table('support_requests')->whereNull('ticket_number')->get()->each(function ($request) {
            $departmentAbbreviations = [
                'secretaria' => 'SEC',
                'professorat' => 'PROF',
                'administració' => 'ADM',
                'informàtica' => 'INF',
                'finances' => 'FIN',
                'acadèmic' => 'ACA',
                'general' => 'GEN',
            ];

            $department = strtolower($request->department ?? 'general');
            $abbr = $departmentAbbreviations[$department] ?? 'GEN';
            $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $request->created_at)->format('Ymd');
            $sequence = str_pad($request->id, 5, '0', STR_PAD_LEFT);
            $ticketNumber = "{$abbr}-{$date}-{$sequence}";
            
            \DB::table('support_requests')
                ->where('id', $request->id)
                ->update(['ticket_number' => $ticketNumber]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_requests', function (Blueprint $table) {
            $table->dropColumn('ticket_number');
        });
    }
};
