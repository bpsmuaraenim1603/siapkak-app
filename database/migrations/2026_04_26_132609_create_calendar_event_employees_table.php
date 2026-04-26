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
        Schema::create('calendar_event_employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('calendar_event_id')
                ->constrained('calendar_events')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();

            $table->unique(['calendar_event_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_event_employees');
    }
};
