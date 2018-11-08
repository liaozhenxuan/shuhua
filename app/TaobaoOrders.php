<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @desc Taobao orders model
 * @author Tom 2017-07-18
 */
class TaobaoOrders extends Model {
    protected $table = 'taobao_orders';

    // protected $fillable = [];
    protected $guarded = [];

    /**
     * @desc get latest orders as a list
     * @param minute: recent minutes
     * @return array
     */
    public function get_latest_list($minute = 1) {
        return $this->where('created', '>=', date("Y-m-d H:i:s", time() - 60 * $minute))->orderBy('created')->get();
    }

    /**
     * @desc get all details of taobao_orders and taobao_orders_detail with specified conditions
     * @param tid 订单号
     * @param num_iid 商品编号
     * @param sku_id SKU编号
     * @param buyer_nick 买家昵称
     * @param receiver_mobile 收货人手机
     * @return array
     */
    public function get_detail($tid=null, $num_iid=null, $sku_id=null, $buyer_nick=null, $receiver_mobile=null) {
        return $this->when(
            $tid, function($query) use ($tid) {
                return $query->where('taobao_orders.tid', $tid);
            }
        )->when(
            $num_iid, function($query) use ($num_iid) {
                return $query->where('taobao_orders.num_iid', $num_iid);
            }
        )->when(
            $sku_id, function($query) use ($sku_id) {
                return $query->where('taobao_orders.sku_id', $sku_id);
            }
        )->when(
            $buyer_nick, function($query) use ($buyer_nick) {
                return $query->when('taobao_orders.buyer_nick', $buyer_nick);
            }
        )->when(
            $receiver_mobile, function($query) use ($receiver_mobile) {
                return $query->when('taobao_orders.receiver_mobile', $receiver_mobile);
            }
        )
        ->leftJoin('taobao_orders_detail', 'taobao_orders.tid', '=', 'taobao_orders_detail.tid')
        // ->select('taobao_orders.*', 'taobao_orders_detail.*')
        ->orderBy('taobao_orders.created', 'DESC')
        ->get();
    }
}
