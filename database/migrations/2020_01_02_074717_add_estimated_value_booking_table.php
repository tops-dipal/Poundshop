<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstimatedValueBookingTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('estimated_value', 32, 2)->default(0)->after('book_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('estimated_value');
        });
    }

}
