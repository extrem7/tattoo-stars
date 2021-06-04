<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->text('text')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();
            $table->boolean('viewed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
}
