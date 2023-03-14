<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();

            $table->string('name');
            $table->string('image_filename')->nullable();
            $table->string('variety')->nullable();
            $table->string('category');
            $table->string('link')->nullable();
            $table->string('green_house')->boolean()->default(false);
            $table->integer('sprouting_time_days_min')->nullable();
            $table->integer('sprouting_time_days_max')->nullable();
            $table->string('sun')->nullable();
            $table->integer('height')->nullable();
            $table->integer('seed_distance_min')->nullable();
            $table->integer('seed_distance_max')->nullable();
            $table->decimal('seed_depth')->nullable();
            $table->date('seeding_start')->nullable();
            $table->date('seeding_end')->nullable();
            $table->date('planting_start')->nullable();
            $table->date('planting_end')->nullable();
            $table->date('harvest_start')->nullable();
            $table->date('harvest_end')->nullable();
            $table->date('inventory_last_checked')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seeds');
    }
};
