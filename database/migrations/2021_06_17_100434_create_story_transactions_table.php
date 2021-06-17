<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoryTransactionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('story_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->tinyInteger('amount');
            $table->unsignedTinyInteger('balance');
            $table->boolean('daily')->nullable();

            $table->timestamp('date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_transactions');
    }
}
