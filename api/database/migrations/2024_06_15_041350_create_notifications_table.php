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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->timestamp('date_captured')->useCurrent();
            $table->string('motion_detected', 100);
            $table->enum('level', ['normal', 'warning', 'danger']);
            $table->enum('status', ['approved', 'ignored']);
            $table->string('snapshot', 255)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id');
            $table->timestamps();
            
            $table->index(['motion_detected', 'level']);
            $table->foreign(['motion_detected', 'level'])
                  ->references(['motion_name', 'level'])
                  ->on('trains');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
