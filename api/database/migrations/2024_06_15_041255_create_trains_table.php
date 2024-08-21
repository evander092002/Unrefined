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
        Schema::create('trains', function (Blueprint $table) {
            $table->id('train_id');
            $table->string('motion_name', 100);
            $table->decimal('threshold', 5, 2);
            $table->enum('level', ['normal', 'warning', 'danger']);
            $table->timestamps();
            
            $table->unique(['motion_name', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trains');
    }
};
