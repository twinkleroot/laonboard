<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeZipDatetimeNickdateOpendateColumnOnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // has not column
            if(!Schema::hasColumn('users', 'nick_date')) {
                $table->date('nick_date')->nullable()->after('nick');
            }
            if(!Schema::hasColumn('users', 'open_date')) {
                $table->date('open_date')->nullable()->after('open');
            }
            if(!Schema::hasColumn('users', 'zip')) {
                $table->char('zip', 5)->nullable()->after('dupinfo');
            }

            // has column
            if(Schema::hasColumn('users', 'datetime')) {
                $table->dropColumn('datetime');
            }
            if(Schema::hasColumn('users', 'zip1')) {
                $table->dropColumn('zip1');
                $table->dropColumn('zip2');
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
        Schema::table('users', function (Blueprint $table) {
            // has not column
            if(!Schema::hasColumn('users', 'zip1')) {
                $table->char('zip1', 3)->nullable();
                $table->char('zip2', 3)->nullable();
            }
            if(!Schema::hasColumn('users', 'datetime')) {
                $table->timestamp('datetime')->nullable();
            }

            // has column
            if(Schema::hasColumn('users', 'zip')) {
                $table->dropColumn('zip');
            }
            if(Schema::hasColumn('users', 'nick_date')) {
                $table->dropColumn('nick_date');
            }
            if(Schema::hasColumn('users', 'open_date')) {
                $table->dropColumn('open_date');
            }
        });
    }
}
