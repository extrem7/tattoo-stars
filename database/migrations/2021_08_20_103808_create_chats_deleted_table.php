<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsDeletedTable extends Migration
{
    public function up(): void
    {
        Schema::create('chats_deleted', function (Blueprint $table) {
            $table->foreignId('chat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->primary(['chat_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats_deleted');
    }
}
