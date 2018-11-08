<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\TaobaoOrders;
use App\TaobaoOrdersDetail;
use Illuminate\Http\Request;
use TaobaoClient\Top\TopClient;
use TaobaoClient\Top\request\TradesSoldIncrementGetRequest;
use TaobaoClient\Top\request\TradeFullinfoGetRequest;
use TaobaoClient\Top\request\TopAuthTokenRefreshRequest;
use TaobaoClient\Top\request\LogisticsDummySendRequest;

/**
 * @desc Taobao orders controller
 * @author Tom 2017-07-19
 */
class TaobaoOrdersController extends Controller {
    private $taobao;
    private $taobao_orders_model;     // TaobaoOrders Model instance
    private $taobao_orders_detail_model;  // TaobaoOrdersDetail Model instance
    private $session_code;      // TaobaoSDK session_code
    private $refresh_token;     // TaobaoSDK refresh_token
    private $time_span;         // how long time between two sync of orders, default: 60 secs

    public function __construct() {
        $this->taobao = new TopClient(env('TAOBAO_SDK_APP_KEY'), env('TAOBAO_SDK_APP_SECRET'));
        $this->taobao_orders_model = new TaobaoOrders();
        $this->taobao_orders_detail_model = new TaobaoOrdersDetail();
        $this->session_code = env('TAOBAO_SDK_SESSION_CODE');
        $this->refresh_token = env('TAOBAO_SDK_REFRESH_TOKEN');
        $this->time_span = 66; // max=60*60*24, 1day, +6s aims to avoid of omitting orders
    }

    /**
     * @desc get latest orders during recent 1 minute
     * @return array
     */
    public function latest_orders_in_1_min() {
        return response()->json(json_encode($this->taobao_orders_model->get_latest_list(1)));
    }

    /**
     * @desc get latest orders during recent 10 minutes
     * @return array
     */
    public function latest_orders_in_10_min() {
        return response()->json($this->taobao_orders_model->get_latest_list(10));
    }

    /**
     * get order detail with specified params
     * @author Tom 2017-07-20
     * @param request Request instance
     * @return array
     */
    public function get_detail(Request $request) {
        $tid = query_param($request, 'tid'); // 订单号
        $num_iid = query_param($request, 'num_iid'); // 商品编号
        $sku_id = query_param($request, 'sku_id'); // SKU编号
        $buyer_nick = query_param($request, 'buyer_nick'); // 买家昵称
        $receiver_mobile = query_param($request, 'receiver_mobile'); // 收货人手机

        return response()->json($this->taobao_orders_model->get_detail($tid, $num_iid, $sku_id, $buyer_nick, $receiver_mobile));
    }

    /**
     * sync orders from taobao api
     * @author Tom 2017-07-21
     * @return json
     */
    public function taobao_orders_crond(Request $request) {
        $req = new TradesSoldIncrementGetRequest;
        $req->setFields('tid, orders.sku_id, post_fee, receiver_name, receiver_state, receiver_city, receiver_district, receiver_town, receiver_address, receiver_zip, receiver_mobile, receiver_phone, received_payment, created, pay_time, has_buyer_message, credit_card_fee, mark_desc, orders.item_meal_name, orders.pic_path, seller_nick, buyer_nick, orders.refund_status, orders.outer_iid, orders.snapshot_url, orders.snapshot, orders.timeout_action_time, orders.buyer_rate, orders.seller_rate, orders.cid, orders.oid, orders.status, orders.title, orders.type, orders.iid, orders.price, orders.num_iid, orders.item_meal_id, orders.num, orders.outer_sku_id, orders.order_from, orders.total_fee, orders.payment, orders.discount_fee, orders.adjust_fee, orders.modified, orders.sku_properties_name, orders.refund_id, orders.is_oversold, orders.is_service_order, orders.end_time, orders.consign_time, orders.shipping_type, orders.bind_oid, orders.logistics_company, orders.invoice_no, orders.store_code, orders.bind_oids, orders.md_qualification, orders.md_fee, orders.inv_type, orders.is_sh_ship, orders.shipper, orders.customization');
        $req->setStartModified('2017-07-01 00:00:00');//(date('Y-m-d H:i:s', time() - $this->time_span));
        $req->setEndModified('2017-07-01 01:00:00');//(date('Y-m-d H:i:s'));
        $req->setPageNo('1');
        $req->setPageSize('100');
        $req->setUseHasNext('true');

        try {
            $count = 0;  // count how many orders successfully saved, and return this value.
            $resp = $this->taobao->execute($req, $this->session_code);
            $result = json_decode(json_encode($resp), true);
            foreach($result['trades']['trade'] as $trade) {
                foreach ($this->expand_orders($trade) as $item) {
                    $this->taobao_orders_model->updateOrCreate(['tid' => $item['tid']], $this->cleanify_array($item));
                    // TODO: need to ensure that requires details
                    $this->taobao_order_detail($request, $item['tid']);
                    // TODO: need to ensure that requires logistics
                    $this->logistics_dummy($request, $item['tid']);
                    $count ++;
                }
            }
            return response()->json(['result' => 0, 'msg' => $count . ' orders saved.']);
        } catch (Exception $e) {
            // TODO check if error msg contains session_code has expired
            if (1 == 2) {
                $this->session_code = $this->update_taobao_session_code();
            }
            Log::error($e);
        }
    }

    /**
     * push orders to chebaba
     * @author Tom 2017-07-24
     * @return json
     */
    protected function orders_save_as_clue(Request $request, $item) {
        try {
            $num_iids = explode(',', env('ORDER_AS_CLUE_NUMIIDS'));
            // if ORDER_AS_CLUE_NUMIIDS is not defined or current num_iid not in ORDER_AS_CLUE_NUMIIDS
            if (empty($num_iids) || !in_array($item['orders_num_iid'], $num_iids)) {
                throw new Exception('no num_iid specified or no need to submit.');
            }
            // TODO
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * sync order detail from taobao api with specified tid
     * @author Tom 2017-07-22
     * @return json
     */
    public function taobao_order_detail(Request $request, $tid) {
        if (empty($tid)) {
            return response()->json('missing tid', 500);
        }

        $req = new TradeFullinfoGetRequest;
        $req->setFields('tid, buyer_message, buyer_memo, seller_memo, trade_from, market, et_shop_id');
        $req->setTid($tid);
        $result = null;

        try {
            $resp = $this->taobao->execute($req, $this->session_code);
            $result = json_decode(json_encode($resp), true);
            $trade = $result['trade'];
            unset($trade['tid_str']);  // remove tid_str field which was not needed but forcely returned by Taobao!
            $return = $this->taobao_orders_detail_model->updateOrCreate(['tid' => $tid], $this->cleanify_array($trade));
            return response()->json(['result' => 0, 'msg' => '', 'data' => $return]);
        } catch (Exception $e) {
            // TODO check if error msg contains session_code has expired
            if (1 == 2) {
                $this->session_code = $this->update_taobao_session_code();
            }
            Log::error($e);
        }
    }

    /**
     * update taobao session code
     * @param request \Request
     * @author Tom 2017-07-23
     * @return session_code string
     */
    public function update_taobao_session_code(Request $request) {
        $req = new TopAuthTokenRefreshRequest;
        $req->setRefreshToken($this->refresh_token);
        $result = null;

        try {
            $resp = $this->taobao->execute($req, $this->session_code);
            $result = json_decode(json_encode($resp), true);
            // TODO: test
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * send goods with specified tid
     * @author Tom 2017-07-23
     * @param tid trade id
     * @return json
     */
    public function logistics_dummy(Request $request, $tid) {
        if (empty($tid)) {
            return response()->json('missing tid', 500);
        }

        $req = new LogisticsDummySendRequest;
        $req->setSellerIp('121.199.160.91');
        $req->setTid($tid);
        $result = null;

        try {
            $resp = $this->taobao->execute($req, $this->session_code);
            $result = json_decode(json_encode($resp), true);
            // TODO: test
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * make orders json into one-layer
     * @author Tom 2017-07-21
     * @param item taobao returned this json stream
     * @return array
     */
    private function expand_orders($item) {
        $results = [];
        $orders = $item['orders']['order'];
        unset($item['orders']);

        // add 'orders_' prefix before k-v pairs of one record
        foreach ($orders as $order) {
            $tmp = $item;
            foreach ($order as $k => $v) {
                $tmp['orders_' . $k] = $v;
            }
            array_push($results, $tmp);
        }
        return $results;
    }

    /**
     * remove keys when value is null, translate true/false into 1/0
     * @author Tom 2017-07-21
     * @param data processed array, before save
     * @return array
     */
    private function cleanify_array($data) {
        $result = [];
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                // translate true/false into 1/0
                if ($v === true) {
                    $v = 1;
                } else if ($v === false) {
                    $v = 0;
                }

                // remove empty pairs after been translated
                if (empty($v)) {
                    continue;
                }

                $result[$k] = $v;
            }
        }
        return $result;
    }

}
