<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSequenceFieldRevision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_revises', function (Blueprint $table) {
           $table->bigInteger('sequence_number')->unsigned()->after('purchase_order_number_sequence')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_revises', function (Blueprint $table) {
           $table->dropColumn('sequence_number');
        });
    }
}
