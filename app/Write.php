<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Write extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function setTableName($tableName)
    {
        $this->table = 'write_' . $tableName;
    }

    public function getTableName()
    {
        return $this->table;
    }

    // 새 게시판 테이블 생성
    public function createWriteTable($table)
    {
        $tableName = 'write_' . $table;

        if(!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->string('reply', 10)->nullable();
                $table->integer('parent')->unsigned()->default(0);
                $table->tinyInteger('is_comment')->default(0);
                $table->integer('comment')->unsigned()->default(0);
                $table->string('comment_reply', 5)->nullable();
                $table->string('ca_name')->nullable();
                $table->enum('option', ['html1', 'html2', 'secret', 'mail'])->nullable();
                $table->string('subject')->nullable();
                $table->text('content')->nullable();
                $table->text('link1')->nullable();
                $table->text('link2')->nullable();
                $table->integer('link1_hit')->unsigned()->default(0);
                $table->integer('link2_hit')->unsigned()->default(0);
                $table->integer('hit')->unsigned()->default(0);
                $table->integer('good')->unsigned()->default(0);
                $table->integer('nogood')->unsigned()->default(0);
                $table->string('user_id_hashkey')->nullable();
                $table->string('password')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('homepage')->nullable();
                $table->timestamps();
                $table->tinyInteger('file')->default(0);
                $table->string('last', 19)->nullable();
                $table->string('ip')->nullable();
                $table->string('facebook_user')->nullable();
                $table->string('twitter_user')->nullable();
                $table->string('subj_1')->nullable();
                $table->string('subj_2')->nullable();
                $table->string('subj_3')->nullable();
                $table->string('subj_4')->nullable();
                $table->string('subj_5')->nullable();
                $table->string('subj_6')->nullable();
                $table->string('subj_7')->nullable();
                $table->string('subj_8')->nullable();
                $table->string('subj_9')->nullable();
                $table->string('subj_10')->nullable();
                $table->string('value_1')->nullable();
                $table->string('value_2')->nullable();
                $table->string('value_3')->nullable();
                $table->string('value_4')->nullable();
                $table->string('value_5')->nullable();
                $table->string('value_6')->nullable();
                $table->string('value_7')->nullable();
                $table->string('value_8')->nullable();
                $table->string('value_9')->nullable();
                $table->string('value_10')->nullable();
            });
            return true;
        } else {
            return false;
        }
    }

}
