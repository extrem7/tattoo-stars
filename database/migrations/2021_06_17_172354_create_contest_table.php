<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestTable extends Migration
{
    public function up(): void
    {
        Schema::create('contest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');

            $table->smallInteger('rating')->default(0);
            $table->boolean('winner')->nullable();

            $table->date('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest');
    }
}
