<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSupplierContactEmailUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_contact', function (Blueprint $table) {
            $table->dropUnique('supplier_contact_email_unique');
            $table->softDeletes();
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
            $table->unique('supplier_contact_email_unique');
            $table->dropSoftDeletes();
        });
    }
}
