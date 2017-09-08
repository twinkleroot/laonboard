<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('boards')) {
            Schema::create('boards', function (Blueprint $table) {
                $table->increments('id');
                $table->string('table_name', '20');
                // groups 테이블에 대한 참조키
                $table->integer('group_id')->unsigned();
                $table->foreign('group_id')->references('id')->on('groups')->onUpdate('cascade')->onDelete('cascade');

                $table->string('subject');
                $table->string('mobile_subject')->nullable();
                $table->enum('device', ['both', 'pc', 'mobile'])->default('both');
                $table->string('admin')->nullable();
                $table->tinyInteger('list_level')->default(0);
                $table->tinyInteger('read_level')->default(0);
                $table->tinyInteger('write_level')->default(0);
                $table->tinyInteger('reply_level')->default(0);
                $table->tinyInteger('comment_level')->default(0);
                $table->tinyInteger('upload_level')->default(0);
                $table->tinyInteger('download_level')->default(0);
                $table->tinyInteger('html_level')->default(0);
                $table->tinyInteger('link_level')->default(0);
                $table->tinyInteger('count_delete')->default(0);
                $table->tinyInteger('count_modify')->default(0);
                $table->integer('read_point')->default(0);
                $table->integer('write_point')->default(0);
                $table->integer('comment_point')->default(0);
                $table->integer('download_point')->default(0);
                $table->tinyInteger('use_category')->default(0);
                $table->text('category_list')->nullable();
                $table->tinyInteger('use_sideview')->default(0);
                $table->tinyInteger('use_file_content')->default(0);
                $table->tinyInteger('use_secret')->default(0);
                $table->tinyInteger('use_dhtml_editor')->default(0);
                $table->tinyInteger('use_rss_view')->default(0);
                $table->tinyInteger('use_recaptcha')->default(0);
                $table->tinyInteger('use_good')->default(0);
                $table->tinyInteger('use_nogood')->default(0);
                $table->tinyInteger('use_name')->default(0);
                $table->tinyInteger('use_signature')->default(0);
                $table->tinyInteger('use_ip_view')->default(0);
                $table->tinyInteger('use_list_view')->default(0);
                $table->tinyInteger('use_list_file')->default(0);
                $table->tinyInteger('use_list_content')->default(0);
                $table->integer('table_width')->default(0);
                $table->integer('subject_len')->default(0);
                $table->integer('mobile_subject_len')->default(0);
                $table->integer('page_rows')->default(0);
                $table->integer('mobile_page_rows')->default(0);
                $table->integer('new')->default(0);
                $table->integer('hot')->default(0);
                $table->integer('image_width')->default(0);
                $table->string('skin')->nullable();
                $table->string('mobile_skin')->nullable();
                $table->string('layout')->nullable();
                $table->text('content_head')->nullable();
                $table->text('mobile_content_head')->nullable();
                $table->text('content_tail')->nullable();
                $table->text('mobile_content_tail')->nullable();
                $table->text('insert_content')->nullable();
                $table->integer('gallery_cols')->default(0);
                $table->integer('gallery_width')->default(0);
                $table->integer('gallery_height')->default(0);
                $table->integer('mobile_gallery_width')->default(0);
                $table->integer('mobile_gallery_height')->default(0);
                $table->integer('upload_size')->default(0);
                $table->tinyInteger('reply_order')->default(0);
                $table->tinyInteger('use_search')->default(0);
                $table->integer('order')->default(0)->nullable();
                $table->integer('count_write')->default(0);
                $table->integer('count_comment')->default(0);
                $table->integer('write_min')->default(0);
                $table->integer('write_max')->default(0);
                $table->integer('comment_min')->default(0);
                $table->integer('comment_max')->default(0);
                $table->text('notice')->nullable();
                $table->tinyInteger('upload_count')->default(0);
                $table->tinyInteger('use_email')->default(0);
                $table->enum('use_cert', ['not-use', 'cert', 'adult', 'hp-cert', 'hp-adult']);
                $table->tinyInteger('use_sns')->default(0);
                $table->string('sort_field')->nullable();
                $table->timestamps();
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
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards');
    }
}
