<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->foreignId('seed_tray_id')->nullable()->after('location')->constrained();
            $table->string('seed_tray_locations')->nullable()->after('seed_tray_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropForeign('seed_tray_id');
            $table->dropColumn('seed_tray_locations');
        });
    }
};
