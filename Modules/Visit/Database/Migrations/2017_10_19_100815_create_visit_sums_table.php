<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitSumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('visit_sums')) {
            Schema::create('visit_sums', function (Blueprint $table) {
                $table->date('date')->primary();
                $table->integer('count')->unsigned();

                $table->index(['count'], 'index1');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('visit_sums')) {
            Schema::dropIfExists('visit_sums');
        }
    }
}
