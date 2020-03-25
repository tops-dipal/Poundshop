<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('promotion_start_at')->after('is_promotional')->comment('only if is_promotional=1')->nullable();
            $table->timestamp('promotion_end_at')->after('promotion_start_at')->comment('only if is_promotional=1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('promotion_start_at');
            $table->dropColumn('promotion_end_at');
        });
    }
}
