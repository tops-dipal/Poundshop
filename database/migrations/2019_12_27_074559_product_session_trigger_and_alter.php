<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductSessionTriggerAndAlter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

         DB::unprepared("
                CREATE TRIGGER ProductSeasonalTrigger 
                AFTER UPDATE ON range_master
                FOR EACH ROW
                BEGIN
                   DECLARE seasonal SMALLINT;
                   DECLARE from_date DATE;
                   DECLARE to_date DATE;
                   SET @seasonal = 0;
                   SET @from_date = NULL;
                   SET @to_date = NULL;
                IF NEW.seasonal_status = 1 THEN
                      SET @seasonal = 1;
                      SET @from_date = DATE_FORMAT(NEW.seasonal_from, '0000-%c-%d');
                      SET @to_date = DATE_FORMAT(NEW.seasonal_to, '0000-%c-%d');
                   END IF;

                UPDATE products
                   set
                       is_seasonal = @seasonal,
                       seasonal_from_date = @from_date,
                       seasonal_to_date = @to_date
                   where
                       buying_category_id = NEW.id;
                END;
        ");

        Schema::table('products', function (Blueprint $table) {
            $table->date('seasonal_from_date')->nullable()->change();
            $table->date('seasonal_to_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

         DB::unprepared('DROP TRIGGER ProductSeasonalTrigger');

        Schema::table('products', function (Blueprint $table) {
            $table->dateTime('seasonal_from_date')->nullable()->change();
            $table->dateTime('seasonal_to_date')->nullable()->change();
        });
    }
}
