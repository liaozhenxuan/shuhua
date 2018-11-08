<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaobaoOrdersTable extends Migration
{
    private $table_name = array('taobao_orders', 'taobao_orders_detail');
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name[0], function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tid')->unique()->comment('订单号');
            $table->bigInteger('orders_sku_id')->nullable()->comment('商品的最小库存单位Sku的id.可以通过taobao.item.sku.get获取详细的Sku信息');
            $table->string('post_fee', 20)->nullable()->comment('邮费。精确到2位小数;单位:元。如:200.07，表示:200元7分');
            $table->string('receiver_name', 100)->nullable()->comment('收货人的姓名');
            $table->string('receiver_state', 100)->nullable()->comment('收货人的所在省份');
            $table->string('receiver_city', 100)->nullable()->comment('收货人的所在城市 注：因为国家对于城市和地区的划分的有：省直辖市和省直辖县级行政区（区级别的）划分的，淘宝这边根据这个差异保存在不同字段里面比如：广东广州：广州属于一个直辖市是放在的receiver_city的字段里面；而河南济源：济源属于省直辖县级行政区划分，是区级别的，放在了receiver_district里面 建议：程序依赖于城市字段做物流等判断的操作，最好加一个判断逻辑：如果返回值里面只有receiver_district参数，该参数作为城市');
            $table->string('receiver_district', 100)->nullable()->comment('收货人的所在地区 注：因为国家对于城市和地区的划分的有：省直辖市和省直辖县级行政区（区级别的）划分的，淘宝这边根据这个差异保存在不同字段里面比如：广东广州：广州属于一个直辖市是放在的receiver_city的字段里面；而河南济源：济源属于省直辖县级行政区划分，是区级别的，放在了receiver_district里面 建议：程序依赖于城市字段做物流等判断的操作，最好加一个判断逻辑：如果返回值里面只有receiver_district参数，该参数作为城市');
            $table->string('receiver_town', 100)->nullable()->comment('收货人街道地址');
            $table->string('receiver_address', 200)->nullable()->comment('收货人的详细地址');
            $table->string('receiver_zip', 10)->nullable()->comment('收货人的邮编');
            $table->string('receiver_mobile', 20)->nullable()->comment('收货人的手机号码');
            $table->string('receiver_phone', 20)->nullable()->comment('收货人的电话号码');
            $table->string('received_payment', 10)->nullable()->comment('卖家实际收到的支付宝打款金额（由于子订单可以部分确认收货，这个金额会随着子订单的确认收货而不断增加，交易成功后等于买家实付款减去退款金额）。精确到2位小数;单位:元。如:200.07，表示:200元7分');
            $table->dateTime('created')->nullable()->comment('交易创建时间。格式:yyyy-MM-dd HH:mm:ss');
            $table->dateTime('pay_time')->nullable()->comment('付款时间。格式:yyyy-MM-dd HH:mm:ss。订单的付款时间即为物流订单的创建时间。');
            $table->tinyInteger('has_buyer_message')->default(0)->comment('判断订单是否有买家留言，有买家留言返回true，否则返回false');
            $table->string('credit_card_fee', 10)->nullable()->comment('使用信用卡支付金额数');
            $table->string('mark_desc', 100)->nullable()->comment('订单出现异常问题的时候，给予用户的描述,没有异常的时候，此值为空');
            $table->string('seller_nick', 100)->nullable()->comment('卖家昵称');
            $table->string('buyer_nick', 100)->nullable()->comment('买家昵称');
            $table->string('orders_item_meal_name', 50)->nullable()->comment('套餐的值。如：M8原装电池:便携支架:M8专用座充:莫凡保护袋');
            $table->string('orders_pic_path', 200)->nullable()->comment('商品图片的绝对路径');
            $table->string('orders_refund_status', 20)->nullable()->comment('退款状态。退款状态。可选值 WAIT_SELLER_AGREE(买家已经申请退款，等待卖家同意) WAIT_BUYER_RETURN_GOODS(卖家已经同意退款，等待买家退货) WAIT_SELLER_CONFIRM_GOODS(买家已经退货，等待卖家确认收货) SELLER_REFUSE_BUYER(卖家拒绝退款) CLOSED(退款关闭) SUCCESS(退款成功)');
            $table->string('orders_outer_iid', 50)->nullable()->comment('商家外部编码(可与商家外部系统对接)。外部商家自己定义的商品Item的id，可以通过taobao.items.custom.get获取商品的Item的信息');
            $table->string('orders_snapshot_url', 200)->nullable()->comment('订单快照URL');
            $table->string('orders_snapshot', 50)->nullable()->comment('订单快照详细信息');
            $table->dateTime('orders_timeout_action_time')->nullable()->comment('订单超时到期时间。格式:yyyy-MM-dd HH:mm:ss');
            $table->tinyInteger('orders_buyer_rate')->default(0)->comment('买家是否已评价。可选值：true(已评价)，false(未评价)');
            $table->tinyInteger('orders_seller_rate')->default(0)->comment('卖家是否已评价。可选值：true(已评价)，false(未评价)');
            $table->bigInteger('orders_cid')->nullable()->comment('交易商品对应的类目ID');
            $table->bigInteger('orders_oid')->nullable()->comment('子订单编号');
            $table->string('orders_status', 50)->nullable()->comment('订单状态（请关注此状态，如果为TRADE_CLOSED_BY_TAOBAO状态，则不要对此订单进行发货，切记啊！）。可选值: TRADE_NO_CREATE_PAY(没有创建支付宝交易) WAIT_BUYER_PAY(等待买家付款) WAIT_SELLER_SEND_GOODS(等待卖家发货,即:买家已付款) WAIT_BUYER_CONFIRM_GOODS(等待买家确认收货,即:卖家已发货) TRADE_BUYER_SIGNED(买家已签收,货到付款专用) TRADE_FINISHED(交易成功) TRADE_CLOSED(付款以后用户退款成功，交易自动关闭) TRADE_CLOSED_BY_TAOBAO(付款以前，卖家或买家主动关闭交易)PAY_PENDING(国际信用卡支付付款确认中)');
            $table->string('orders_title', 50)->nullable()->comment('商品标题');
            $table->string('orders_type', 50)->nullable()->comment('交易类型');
            $table->string('orders_iid', 50)->nullable()->comment('商品的字符串编号(注意：iid近期即将废弃，请用num_iid参数)');
            $table->string('orders_price', 10)->nullable()->comment('商品价格。精确到2位小数;单位:元。如:200.07，表示:200元7分');
            $table->bigInteger('orders_num_iid')->comment('商品数字ID');
            $table->bigInteger('orders_item_meal_id')->nullable()->comment('套餐ID');
            $table->integer('orders_num')->nullable()->comment('购买数量。取值范围:大于零的整数');
            $table->bigInteger('orders_outer_sku_id')->nullable()->comment('外部网店自己定义的Sku编号');
            $table->string('orders_order_from', 20)->nullable()->comment('子订单来源,如jhs(聚划算)、taobao(淘宝)、wap(无线)');
            $table->string('orders_total_fee', 10)->nullable()->comment('应付金额（商品价格 * 商品数量 + 手工调整金额 - 子订单级订单优惠金额）。精确到2位小数;单位:元。如:200.07，表示:200元7分');
            $table->string('orders_payment', 10)->nullable()->comment('子订单实付金额。精确到2位小数，单位:元。如:200.07，表示:200元7分。对于多子订单的交易，计算公式如下：payment = price * num + adjust_fee - discount_fee ；单子订单交易，payment与主订单的payment一致，对于退款成功的子订单，由于主订单的优惠分摊金额，会造成该字段可能不为0.00元。建议使用退款前的实付金额减去退款单中的实际退款金额计算。');
            $table->string('orders_discount_fee', 10)->nullable()->comment('子订单级订单优惠金额。精确到2位小数;单位:元。如:200.07，表示:200元7分');
            $table->string('orders_adjust_fee', 10)->nullable()->comment('手工调整金额.格式为:1.01;单位:元;精确到小数点后两位.');
            $table->dateTime('orders_modified')->nullable()->comment('订单修改时间，目前只有taobao.trade.ordersku.update会返回此字段。');
            $table->string('orders_sku_properties_name', 50)->nullable()->comment('SKU的值。如：机身颜色:黑色;手机套餐:官方标配');
            $table->bigInteger('orders_refund_id')->nullable()->comment('最近退款ID');
            $table->tinyInteger('orders_is_oversold')->nullable()->comment('是否超卖');
            $table->tinyInteger('orders_is_service_order')->nullable()->comment('是否是服务订单，是返回true，否返回false。');
            $table->dateTime('orders_end_time')->nullable()->comment('子订单的交易结束时间说明：子订单有单独的结束时间，与主订单的结束时间可能有所不同，在有退款发起的时候或者是主订单分阶段付款的时候，子订单的结束时间会早于主订单的结束时间，所以开放这个字段便于订单结束状态的判断');
            $table->string('orders_consign_time', 20)->nullable()->comment('子订单发货时间，当卖家对订单进行了多次发货，子订单的发货时间和主订单的发货时间可能不一样了，那么就需要以子订单的时间为准。（没有进行多次发货的订单，主订单的发货时间和子订单的发货时间都一样）');
            $table->string('orders_shipping_type', 20)->nullable()->comment('子订单的运送方式（卖家对订单进行多次发货之后，一个主订单下的子订单的运送方式可能不同，用order.shipping_type来区分子订单的运送方式）');
            $table->bigInteger('orders_bind_oid')->nullable()->comment('捆绑的子订单号，表示该子订单要和捆绑的子订单一起发货，用于卖家子订单捆绑发货');
            $table->string('orders_logistics_company', 50)->nullable()->comment('子订单发货的快递公司名称');
            $table->string('orders_invoice_no', 20)->nullable()->comment('子订单所在包裹的运单号');
            $table->string('orders_store_code', 20)->nullable()->comment('发货的仓库编码');
            $table->string('orders_bind_oids', 200)->nullable()->comment('bind_oid字段的升级，支持返回绑定的多个子订单，多个子订单以半角逗号分隔');
            $table->string('orders_md_qualification', 50)->nullable()->comment('免单资格属性');
            $table->string('orders_md_fee', 10)->nullable()->comment('免单金额');
            $table->string('orders_inv_type', 20)->nullable()->comment('库存类型：6为在途');
            $table->tinyInteger('orders_is_sh_ship')->nullable()->comment('是否发货');
            $table->string('orders_shipper', 20)->nullable()->comment('仓储信息');
            $table->text('orders_customization')->nullable()->comment('定制信息');
            $table->text('remark')->nullable()->comment('');
            $table->timestamps();
        });

        Schema::create($this->table_name[1], function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tid')->comment('订单编号');
            $table->string('buyer_message', 200)->nullable()->comment('买家留言');
            $table->string('buyer_memo', 200)->nullable()->comment('买家备注（与淘宝网上订单的买家备注对应，只有买家才能查看该字段）');
            $table->string('seller_memo', 200)->nullable()->comment('卖家备注（与淘宝网上订单的卖家备注对应，只有卖家才能查看该字段）');
            $table->string('trade_from', 20)->nullable()->comment('交易内部来源。WAP(手机);HITAO(嗨淘);TOP(TOP平台);TAOBAO(普通淘宝);JHS(聚划算)一笔订单可能同时有以上多个标记，则以逗号分隔');
            $table->string('market', 100)->nullable()->comment('垂直市场');
            $table->bigInteger('et_shop_id')->nullable()->comment('扫码购关联门店');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name[0]);
        Schema::dropIfExists($this->table_name[1]);
    }
}
