<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersInformationTable extends Migration
{
    public function up(): void
    {
        Schema::create('users_information', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->unique()->constrained()->onDelete('cascade');

            $table->foreignId('gender_id')->nullable()->constrained('user_genders')->nullOnDelete();
            $table->date('birthday')->nullable();

            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('address')->nullable();

            $table->string('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_information');
    }
}
