<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSupplerMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
           $table->smallInteger('payment_term')->default('1')->comment('1 - Cash on delivery, 2 - Perform Invoice, 3 - supplier_master.payment_days days after delivery date')->change();
           $table->decimal('retro_percent_discount', 10, 2)->nullable()->after('payment_days');
           $table->date('retro_to_date')->nullable()->after('payment_days');
           $table->date('retro_from_date')->nullable()->after('payment_days');
           $table->decimal('retro_amount', 10, 2)->nullable()->after('payment_days');
           $table->decimal('period_percent_discount', 10, 2)->nullable()->after('payment_days');
           $table->tinyInteger('period_discount_days')->nullable()->after('payment_days');
           $table->tinyInteger('allow_period_discount')->default('0')->comment('0-No,1-Yes')->after('payment_days');
           $table->decimal('overall_percent_discount', 10, 2)->nullable()->after('payment_days');
           $table->tinyInteger('allow_overall_discount')->default('0')->comment('0-No,1-Yes')->after('payment_days');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
            $table->smallInteger('payment_term')->default('1')->comment('1-30 days before delivery(default),2-30 days after delivery,3-30 days net payment,4-on delivery,5-retro discount')->change();
            $table->dropColumn('allow_overall_discount');
            $table->dropColumn('overall_percent_discount');
            $table->dropColumn('allow_period_discount');
            $table->dropColumn('period_discount_days');
            $table->dropColumn('period_percent_discount');
            $table->dropColumn('retro_amount');
            $table->dropColumn('retro_from_date');
            $table->dropColumn('retro_to_date');
            $table->dropColumn('retro_percent_discount');
        });
    }
}
