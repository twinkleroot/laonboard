<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Content\Models\Content;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('contents')) {
            Schema::create('contents', function (Blueprint $table) {
                $table->increments('id');
                $table->string('content_id', 20);
                $table->tinyInteger('html')->nullable()->default(0);
                $table->string('subject')->nullable();
                $table->longText('content')->nullable();
                $table->longText('mobile_content')->nullable();
                $table->string('skin')->nullable()->default('');
                $table->string('mobile_skin')->nullable()->default('');
                $table->tinyInteger('tag_filter_use')->nullable()->default(0);
                $table->integer('hit')->nullable();
                $table->tinyInteger('show')->nullable()->default(1);
            });

            // 초기 데이터 생성
            $ids = ['company', 'privacy', 'provision'];
            $subjects = ['회사소개', '개인정보 처리방침', '서비스 이용약관'];
            $contents = [
                '<p align=center><b>회사소개에 대한 내용을 입력하십시오.</b></p>',
                '<p align=center><b>개인정보 처리방침에 대한 내용을 입력하십시오.</b></p>',
                '<p align=center><b>서비스 이용약관에 대한 내용을 입력하십시오.</b></p>'
            ];
            for($i=0; $i<notNullCount($ids); $i++) {
                Content::insert([
                    'content_id' => $ids[$i],
                    'html' => 1,
                    'subject' => $subjects[$i],
                    'content' => $contents[$i]
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('contents')) {
            Schema::dropIfExists('contents');
        }
    }
}
