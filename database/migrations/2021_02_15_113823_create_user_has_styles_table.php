<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasStylesTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_has_styles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('style_id')->constrained('user_styles')->onDelete('cascade');

            $table->primary(['user_id', 'style_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_has_styles');
    }
}
