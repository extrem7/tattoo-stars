<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisingTopsTable extends Migration
{
    public function up(): void
    {
        Schema::create('advertising_tops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('days');

            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertising_tops');
    }
}
