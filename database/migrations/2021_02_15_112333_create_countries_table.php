<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->char('id', 2)->primary()->unique();
            $table->string('name', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
}
