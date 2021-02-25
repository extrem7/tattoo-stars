<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiPagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('api_pages', function (Blueprint $table) {
            $table->id();

            $table->json('title');
            $table->json('body')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_pages');
    }
}
