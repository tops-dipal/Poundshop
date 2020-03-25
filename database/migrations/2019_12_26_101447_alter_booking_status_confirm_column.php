<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class AlterBookingStatusConfirmColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
           // DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('tinyinteger', 'string');
            $table->dropColumn('is_confirmed');
            $table->smallInteger('status')->tinyInteger('status')->comment('1=>Reserve with PO,2=>Reserve without PO, 3=>Confirmed,3=>Not Arrived,4=>Arrived,5=>Receiving,6=>Completed ')->change();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
           // DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('tinyinteger', 'string');
            $table->smallInteger('is_confirmed')->tinyInteger('is_confirmed')->default(0)->comment(' 1=>confirmed,0=>not confirmed ')->after('status');
        });
    }
}
