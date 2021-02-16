<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /* php artisan migrate:refresh --path=/database/migrations/2021_02_15_112334_create_cities_table.php */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();

            $table->char('country_id', 2);
            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();

            $table->string('name', 50);
            $table->double('lat');
            $table->double('lng');
            $table->unsignedInteger('population')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
}
