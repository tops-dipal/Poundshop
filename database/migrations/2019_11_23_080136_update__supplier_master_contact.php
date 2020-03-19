<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSupplierMasterContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_contact', function (Blueprint $table) {
            $table->smallInteger('is_primary')->default('0')->comment('0-No,1-Yes')->change();
            $table->string('mobile',20)->after('phone')->nullable();
        });

        Schema::table('supplier_master', function (Blueprint $table) {
            $table->text('term_condition')->after('bank_iban_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_contact', function (Blueprint $table) {
            $table->smallInteger('is_primary')->default('1')->comment('0-No,1-Yes')->change();
            $table->dropColumn('mobile');            
        });
        
        Schema::table('supplier_master', function (Blueprint $table) {
            $table->dropColumn('term_condition');            
        });
    }
}
