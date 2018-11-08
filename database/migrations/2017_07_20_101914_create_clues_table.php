<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCluesTable extends Migration
{
    private $table_name = 'clues';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_name', 60)->comment('用户名称');
            $table->tinyInteger('gender')->nullable()->comment('性别');
            $table->string('phone', 20)->comment('手机');
            $table->string('dealer_code', 20)->nullable()->comment('经销商编码');
            $table->string('series_id', 32)->nullable()->comment('车系ID');
            $table->string('model_id', 32)->nullable()->comment('车型ID');
            $table->string('ip', 20)->default('0.0.0.0')->comment('用户IP地址');
            $table->string('send_channel', 20)->default('CHEBABA')->comment('下发渠道');
            $table->tinyInteger('is_send')->default(1)->comment('是否下发');
            $table->string('activity_name', 100)->default('常规通用留资')->comment('活动名称');
            $table->string('pageid', 60)->comment('KEY值');
            $table->string('custom_url', 200)->comment('自定义链接');
            $table->string('smartcode', 60)->nullable()->comment('SC值');
            $table->text('ext_json')->nullable()->comment('扩展字段JSON结构');
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
