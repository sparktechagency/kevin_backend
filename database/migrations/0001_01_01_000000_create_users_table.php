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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('email',255)->unique()->index();
            $table->string('password')->nullable();
            $table->string('employee_pin')->nullable()->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('avatar')->default('default/profile.png');
            $table->boolean('is_banned')->default(false)->index();
            $table->boolean('is_notification')->default(true)->index();
            $table->enum('role', ['EMPLOYEE','MANAGER','USER','ADMIN','MENTOR'])->default('USER')->index();

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
