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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('type')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider_token')->nullable();
            $table->string('device_token')->nullable();
            $table->boolean('activated')->nullable()->default(true);
            $table->string('user_source')->nullable();
            $table->string('user_login_from')->nullable()->comment('foodcout or electronic');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verify_at')->nullable()->comment('when phone verifyed');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
