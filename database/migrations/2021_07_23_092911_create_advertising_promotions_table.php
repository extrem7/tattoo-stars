<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisingPromotionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('advertising_promotions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->constrained()->onDelete('cascade');

            $table->char('country_id', 2)->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();

            $table->enum('account_type', ['users', 'other'])->nullable();

            $table->unsignedMediumInteger('clicks')->default(0);
            $table->unsignedMediumInteger('views')->default(0);
            $table->unsignedMediumInteger('budget');

            $table->boolean('on_pause')->nullable();
            $table->boolean('verified')->nullable();
            $table->string('reject_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertising_promotions');
    }
}
