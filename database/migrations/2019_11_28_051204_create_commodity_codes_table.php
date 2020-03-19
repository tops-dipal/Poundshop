<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommodityCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique()->index();
            $table->tinyInteger('is_default')->default(1)->comment('1->yes,0->no');
            $table->decimal('vat',5,2)->comment('vat in %')->nullable();
            $table->decimal('import_duty',5,2)->comment('import duty in %')->nullable();
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->unsigned()->index()->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commodity_codes');
    }
}
