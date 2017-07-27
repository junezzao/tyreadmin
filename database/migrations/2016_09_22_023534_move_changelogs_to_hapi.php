<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveChangelogsToHapi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $frontDb = env('DB_DATABASE');
        $backDb = env('DB_DATABASE_TYREAPI');
        
        Schema::create($backDb.'.changelogs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title',100);
            $table->text('content');
            $table->timestamps();
        });

        if (Schema::hasTable('changelogs')) {
            // 
            DB::statement("INSERT INTO `".$backDb."`.`changelogs` SELECT * from `".$frontDb."`.`changelogs`;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop(env('DB_DATABASE_TYREAPI').'.changelogs');
    }
}
