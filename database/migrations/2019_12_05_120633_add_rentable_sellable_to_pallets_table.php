<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRentableSellableToPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pallets_master', function (Blueprint $table) {
            $table->tinyInteger('returnable')->default('0')->comment('1 - Yes, 0 - No')->after('barcode');
            $table->tinyInteger('sellable')->default('0')->comment('1 - Yes, 0 - No')->after('returnable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pallets_master', function (Blueprint $table) {
            $table->dropColumn('returnable');
            $table->dropColumn('sellable');
        });
    }
}
