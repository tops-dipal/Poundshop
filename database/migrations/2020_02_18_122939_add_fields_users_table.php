<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('replen_sort_by', 100)->after('replen_job_access')->comment('store sort order for replen list')->nullable();
            $table->string('replen_sort_direction', 100)->after('replen_sort_by')->comment('store sort order for replen list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('replen_sort_by');
            $table->dropColumn('replen_sort_direction');
        });
    }
}
