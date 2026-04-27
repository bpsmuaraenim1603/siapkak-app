<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dateTime('send_at')->nullable()->after('end_datetime');
            $table->string('whatsapp_status')->default('pending')->after('send_at');
            $table->dateTime('whatsapp_sent_at')->nullable()->after('whatsapp_status');
            $table->text('whatsapp_message_snapshot')->nullable()->after('whatsapp_sent_at');
            $table->text('whatsapp_error_message')->nullable()->after('whatsapp_message_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropColumn([
                'send_at',
                'whatsapp_status',
                'whatsapp_sent_at',
                'whatsapp_message_snapshot',
                'whatsapp_error_message',
            ]);
        });
    }
};