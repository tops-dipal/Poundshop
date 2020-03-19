<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('category_id')->nullable()->comment('Category Id of magento');
            $table->integer('parent_id')->nullable()->comment('Foregin Key of self');
            $table->tinyInteger('is_active')->nullable()->comment('0 - Yes, 1 - No');
            $table->tinyInteger('position')->nullable();
            $table->tinyInteger('level')->nullable();
            $table->tinyInteger('store_id')->nullable();
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
        Schema::dropIfExists('magento_categories');
    }
}
