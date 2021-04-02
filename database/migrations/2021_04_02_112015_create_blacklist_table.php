<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlacklistTable extends Migration
{
    public function up(): void
    {
        Schema::create('blacklist', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('blocked_id')->constrained('users')->onDelete('cascade');

            $table->timestamp('blocked_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_blocked');
    }
}
