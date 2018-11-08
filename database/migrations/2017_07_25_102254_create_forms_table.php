<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    private $table_name = 'forms';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 200)->comment('活动标题');
            $table->text('dealer_codes')->nullable()->comment('经销商組ID');
            $table->text('series_ids')->nullable()->comment('车系ID，多个用逗号隔开');
            $table->text('model_ids')->nullable()->comment('车型ID，多个用逗号隔开');
            $table->string('submit_button', 40)->default('获取优惠')->comment('提交按钮文字');
            $table->integer('start_date')->comment('起始日期');
            $table->integer('end_date')->comment('截止日期');
            $table->string('pageid', 60)->comment('KEY值');
            $table->string('sms_template', 200)->comment('短信模板，支持固定参数');
            $table->text('ext_fields')->comment('扩展字段，默认type=input\n文本框：[{“cardno”: “身份证号码”}]  <input type=“text” name=”cardno” placeholder=“身份证号码”>\n下拉框/单选：[{“type”: “select”, “sex”: {“1”: “男”, “0”: “女”}}] <select name=“sex”><option name=“1”>男</option><option name=“0”>女</option></select>');
            $table->text('image_urls')->comment('多图滚动');
            $table->tinyInteger('need_smsverify')->default(1)->comment('是否需要短信验证码');
            $table->tinyInteger('is_send')->default(1)->comment('是否直接下发');
            $table->text('ext_desc')->nullable()->comment('活动规则内容配置');
            $table->text('notify_text')->comment('留资完成弹窗提示文本模板');
            $table->string('custom_url', 200)->unique()->comment('自定义链接');
            $table->string('completed_url', 200)->default('https://dongfeng-nissan.tmall.com')->comment('留资完成跳转链接');
            $table->tinyInteger('is_enable')->default(1)->comment('是否可用');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name);
    }
}
