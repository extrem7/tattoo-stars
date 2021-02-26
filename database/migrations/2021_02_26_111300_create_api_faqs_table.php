<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiFaqsTable extends Migration
{
    public function up(): void
    {
        Schema::create('api_faqs', function (Blueprint $table) {
            $table->id();

            $table->json('question');
            $table->json('answer');

            $table->unsignedBigInteger('order')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_faqs');
    }
}
