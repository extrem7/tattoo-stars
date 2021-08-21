<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBigColumnInUsersInformationTable extends Migration
{
    public function up(): void
    {
        Schema::table('users_information', function (Blueprint $table) {
            $table->string('bio', 510)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users_information', function (Blueprint $table) {
            $table->string('bio')->nullable()->change();
        });
    }
}
