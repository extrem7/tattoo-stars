<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestVotesTable extends Migration
{
    public function up(): void
    {
        Schema::create('contest_votes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_id')->constrained('contest')->onDelete('cascade');

            $table->primary(['user_id', 'work_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_votes');
    }
}
