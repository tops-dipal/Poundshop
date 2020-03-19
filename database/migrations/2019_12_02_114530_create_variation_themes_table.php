<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariationThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_themes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('variation_theme_name', 50)->nullable();
            $table->string('variation_theme_1', 20)->nullable();
            $table->string('variation_theme_2', 20)->nullable();
            $table->tinyInteger('combination_type')->nullable();
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
        Schema::dropIfExists('variation_themes');
    }
}
