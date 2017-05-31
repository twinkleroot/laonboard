<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('email', 50)->unique();
                $table->string('password', 60)->nullable();
                $table->string('nick');
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
                $table->char('zip', 5)->nullable();
                $table->string('addr1')->nullable();
                $table->string('addr2')->nullable();
                $table->string('addr_jibeon')->nullable();
                $table->text('signature')->nullable();
                $table->integer('recommend', false, true)->nullable();
                $table->integer('point')->nullable()->default(0);
                $table->timestamp('today_login')->dafault(Carbon::now())->index();
                $table->string('login_ip')->nullable();
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
                $table->string('id_hashkey')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->string('extra_1')->nullable();
                $table->string('extra_2')->nullable();
                $table->string('extra_3')->nullable();
                $table->string('extra_4')->nullable();
                $table->string('extra_5')->nullable();
                $table->string('extra_6')->nullable();
                $table->string('extra_7')->nullable();
                $table->string('extra_8')->nullable();
                $table->string('extra_9')->nullable();
                $table->string('extra_10')->nullable();

                $table->timestamp('updated_at')->default(Carbon::now())->index()->change();
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
        Schema::dropIfExists('users');
    }
}
