<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class UpdateCommodityProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('commodity_code', 'commodity_code_id');
            
            $table->smallInteger('on_hold')->default('0')->comment('1 - Yes, 0 - No')->after('comment');
            
            $table->smallInteger('is_essential')->default('0')->comment('1 - Yes, 0 - No')->after('comment');
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
            $table->renameColumn('commodity_code_id','commodity_code');
            $table->dropColumn('is_essential');
            $table->dropColumn('on_hold');
        });
    }
}
