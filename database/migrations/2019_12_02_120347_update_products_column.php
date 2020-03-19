<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('vat_percent');

            $table->tinyInteger('vat_type')->default('0')->comment('0 - Standard, 1 - Zero Rated, 3 - Mixed')->nullable()->after('bulk_selling_quantity');

            $table->dateTime('seasonal_to_date')->after('on_hold')->nullable();
            
            $table->dateTime('seasonal_from_date')->after('on_hold')->nullable();
            
            $table->tinyInteger('is_seasonal')->default('0')->comment('0 - No, 1 - Yes')->after('on_hold')->nullable();

            
            $table->tinyInteger('info_missing')->default('0')->comment('0 - NO, 1 - Yes')->after('on_hold')->nullable();


            $table->tinyInteger('mp_image_missing')->default('0')->comment('0 - No, 1 - Yes')->after('on_hold')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('vat_percent')->after('bulk_selling_quantity')->nullable();

            $table->dropColumn('vat_type');
            $table->dropColumn('seasonal_from_date');
            $table->dropColumn('seasonal_to_date');
            $table->dropColumn('is_seasonal');
            $table->dropColumn('info_missing');
            $table->dropColumn('mp_image_missing');
        });
    }
}
