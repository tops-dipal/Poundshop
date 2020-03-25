<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChangePutAwayField extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('put_aways', function (Blueprint $table) {
            $table->smallInteger('put_away_type')->tinyInteger('put_away_type')->comment('0=>Default,1=>Material Receipt,2=>Replan')->change();
            $table->bigInteger('location_id')->unsigned()->index()->nullable()->after('product_id')->comment('reference to location master table');
            $table->foreign('location_id')->references('id')->on('locations_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('put_aways', function (Blueprint $table) {
            $table->smallInteger('put_away_type')->tinyInteger('put_away_type')->comment('0=>Default,1=>Material Receipt,2=>Replan')->change();
            $table->dropColumn('location_id');
            $table->dropForeign('location_id');
        });
    }

}
