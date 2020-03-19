<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPoProductsConflictMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('po_products', function (Blueprint $table) {
           $table->decimal('import_duty_in_amount',32,2)->nullable()->after('import_duty');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('po_products', function (Blueprint $table) {
            $table->dropColumn('import_duty_in_amount');


         });
    }
}
