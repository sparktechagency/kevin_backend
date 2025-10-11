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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_email')->unique();
            $table->string('company_phone');
            $table->text('company_address')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('manager_full_name');
            $table->string('manager_email')->unique();
            $table->string('manager_phone');
            $table->string('manager_code')->unique()->nullable(); // auto generated
            $table->boolean('send_welcome_email')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
