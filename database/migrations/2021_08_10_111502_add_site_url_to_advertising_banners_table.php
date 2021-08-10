<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiteUrlToAdvertisingBannersTable extends Migration
{
    public function up(): void
    {
        Schema::table('advertising_banners', function (Blueprint $table) {
            $table->string('site_url')->nullable()->after('redirect_to_site');
        });
    }

    public function down(): void
    {
        Schema::table('advertising_banners', function (Blueprint $table) {
            $table->dropColumn('site_url');
        });
    }
}
