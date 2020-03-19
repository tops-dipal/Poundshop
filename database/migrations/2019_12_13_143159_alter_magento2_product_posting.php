<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMagento2ProductPosting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        $this->down();
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->smallInteger('is_revised')->default(0)->comment('0 - NO, 1- YES')->after('is_posted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->dropColumn('is_revised');
        });
    }
}
