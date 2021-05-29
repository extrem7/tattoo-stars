<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoryMarksTable extends Migration
{
    public function up(): void
    {
        Schema::create('story_marks', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('story_id')->constrained()->onDelete('cascade');

            $table->boolean('is_dislike')->nullable();

            $table->primary(['user_id', 'story_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_marks');
    }
}
