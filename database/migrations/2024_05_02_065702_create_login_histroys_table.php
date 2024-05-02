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
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->string('email')->index()->nullable();
            $table->string('request_ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('request_for', ['Login', 'Logout'])->nullable();
            $table->string('remark', 50)->nullable();
            $table->enum('status', ['Success', 'Failed'])->nullable();
            $table->timestamp('request_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
