<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStylesTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_styles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_styles');
    }
}
