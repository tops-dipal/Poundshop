<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropVatImportdutyAddDescToCommodityCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodity_codes', function (Blueprint $table) {
            $table->dropColumn('vat');
            $table->dropColumn('import_duty');
            if (!Schema::hasColumn('commodity_codes','desc'))
            {
                 $table->text('desc')->nullable();
             }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commodity_codes', function (Blueprint $table) {
            $table->decimal('vat',5,2)->comment('vat in %')->nullable();
            $table->decimal('import_duty',5,2)->comment('import duty in %')->nullable();
        });
    }
}
