<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nick')->nullable();
            $table->date('nick_date')->nullable();
            $table->string('homepage')->nullable();
            $table->tinyInteger('level')->nullable()->default(0);
            $table->char('sex', 1)->nullable();
            $table->string('birth')->nullable();
            $table->string('tel')->nullable();
            $table->string('hp')->nullable();
            $table->string('certify', 20)->nullable();
            $table->tinyInteger('adult')->nullable()->default(0);
            $table->string('dupinfo')->nullable();
            $table->char('zip1', 3)->nullable();
            $table->char('zip2', 3)->nullable();
            $table->string('addr1')->nullable();
            $table->string('addr2')->nullable();
            $table->string('addr3')->nullable();
            $table->string('addr_jibeon')->nullable();
            $table->text('signature')->nullable();
            $table->string('recommend')->nullable();
            $table->integer('point')->nullable()->default(0);
            $table->timestamp('today_login');
            $table->string('login_ip')->nullable();
            $table->timestamp('datetime')->nullable();
            $table->string('ip')->nullable();
            $table->string('leave_date', 8)->nullable();
            $table->string('intercept_date', 8)->nullable();
            $table->timestamp('email_certify')->nullable();
            $table->string('email_certify2')->nullable();
            $table->text('memo')->nullable();
            $table->string('lost_certify')->nullable();
            $table->tinyInteger('mailing')->nullable()->default(0);
            $table->tinyInteger('sms')->nullable()->default(0);
            $table->tinyInteger('open')->nullable()->default(0);
            $table->date('open_date')->nullable();
            $table->text('profile')->nullable();
            $table->string('memo_call')->nullable();

            $table->index('today_login');
            $table->index('updated_at');
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
            if(Schema::hasColumn('users', 'nick')) {
                $table->dropColumn('nick');
            }
            if(Schema::hasColumn('users', 'nick_date')) {
                $table->dropColumn('nick_date');
            }
            $table->dropColumn('homepage');
            $table->dropColumn('level');
            $table->dropColumn('sex');
            $table->dropColumn('birth');
            $table->dropColumn('tel');
            $table->dropColumn('hp');
            $table->dropColumn('certify');
            $table->dropColumn('adult');
            $table->dropColumn('dupinfo');
            if(Schema::hasColumn('users', 'zip1')) {
                $table->dropColumn('zip1');
            }
            if(Schema::hasColumn('users', 'zip2')) {
                $table->dropColumn('zip2');
            }
            $table->dropColumn('addr1');
            $table->dropColumn('addr2');
            if(Schema::hasColumn('users', 'addr3')) {
                $table->dropColumn('addr3');
            }
            $table->dropColumn('addr_jibeon');
            $table->dropColumn('signature');
            if(Schema::hasColumn('users', 'recommend')) {
                $table->dropColumn('recommend');
            }
            $table->dropColumn('point');
            $table->dropColumn('today_login');
            $table->dropColumn('login_ip');
            if(Schema::hasColumn('users', 'datetime')) {
                $table->dropColumn('datetime');
            }
            $table->dropColumn('ip');
            $table->dropColumn('leave_date');
            $table->dropColumn('intercept_date');
            $table->dropColumn('email_certify');
            $table->dropColumn('email_certify2');
            $table->dropColumn('memo');
            $table->dropColumn('lost_certify');
            $table->dropColumn('mailing');
            $table->dropColumn('sms');
            $table->dropColumn('open');
            if(Schema::hasColumn('users', 'open_date')) {
                $table->dropColumn('open_date');
            }
            $table->dropColumn('profile');
            $table->dropColumn('memo_call');
        });
    }
}
