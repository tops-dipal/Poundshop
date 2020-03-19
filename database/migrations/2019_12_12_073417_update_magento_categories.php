<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMagentoCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_categories', function (Blueprint $table) {
            $table->string('path')->after('level')->nullable();
            $table->string('structure')->after('path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_categories', function (Blueprint $table) {
            $table->dropColumn('path');
            $table->dropColumn('structure');
        });
    }
}
