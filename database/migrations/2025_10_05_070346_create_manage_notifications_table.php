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
        Schema::create('manage_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message');
            $table->string('target_audience');
            $table->enum('status', ['draft', 'sent'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_notifications');
    }
};
