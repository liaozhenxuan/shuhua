<?php

namespace App\Http\Controllers;

use App\Clues;
use App\Common;
use App\TaobaoOrders;
use App\TaobaoOrdersDetail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use TaobaoClient\Top\Request\LogisticsDummySendRequest;
use TaobaoClient\Top\Request\TopAuthTokenRefreshRequest;
use TaobaoClient\Top\Request\TradeFullinfoGetRequest;
use TaobaoClient\Top\TopClient;
use Log;
use TaobaoClient\Top\Request\TradesSoldIncrementGetRequest;


class CatchTaobaoOrdersController extends Controller
{
    private $taobao;
    private $taobao_orders_model;     // TaobaoOrders Model instance
    private $taobao_orders_detail_model;  // TaobaoOrdersDetail Model instance
    private $session_code;      // TaobaoSDK session_code
    private $refresh_token;     // TaobaoSDK refresh_token
    private $time_span;         // how long time between two sync of orders, default: 60 secs
    private $clues_model;
    private $full_info;
    private $cache_map_tmall;

    protected $counp_api_sign_key = 'ChEbAbA^&*_5439810@1';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catchTaobaoOrders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取淘宝订单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->taobao = new TopClient(env('TAOBAO_SDK_APP_KEY'), env('TAOBAO_SDK_APP_SECRET'));
        $this->taobao_orders_model = new TaobaoOrders();
        $this->taobao_orders_detail_model = new TaobaoOrdersDetail();
        $this->session_code = env('TAOBAO_SDK_SESSION_CODE');
        $this->refresh_token = env('TAOBAO_SDK_REFRESH_TOKEN');
        $this->time_span = 66; // max=60*60*24, 1day, +6s aims to avoid of omitting orders
        $this->clues_model = new Clues();
        $this->cache_map_tmall = $this->get_tmall_maps();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function catch_orders()
    {
        $req = new TradesSoldIncrementGetRequest();
        $req->setFields('tid, orders.sku_id, post_fee, receiver_name, receiver_state, receiver_city, receiver_district, receiver_town, receiver_address, receiver_zip, receiver_mobile, receiver_phone, received_payment, created, pay_time, has_buyer_message, credit_card_fee, mark_desc, orders.item_meal_name, orders.pic_path, seller_nick, buyer_nick, orders.refund_status, orders.outer_iid, orders.snapshot_url, orders.snapshot, orders.timeout_action_time, orders.buyer_rate, orders.seller_rate, orders.cid, orders.oid, orders.status, orders.title, orders.type, orders.iid, orders.price, orders.num_iid, orders.item_meal_id, orders.num, orders.outer_sku_id, orders.order_from, orders.total_fee, orders.payment, orders.discount_fee, orders.adjust_fee, orders.modified, orders.sku_properties_name, orders.refund_id, orders.is_oversold, orders.is_service_order, orders.end_time, orders.consign_time, orders.shipping_type, orders.bind_oid, orders.logistics_company, orders.invoice_no, orders.store_code, orders.bind_oids, orders.md_qualification, orders.md_fee, orders.inv_type, orders.is_sh_ship, orders.shipper, orders.customization');
        $req->setStartModified(date('Y-m-d H:i:s', time() - $this->time_span));//(date('Y-m-d H:i:s', time() - $this->time_span));
        $req->setEndModified(date('Y-m-d H:i:s'));//(date('Y-m-d H:i:s'));
        $req->setPageNo('1');
        $req->setPageSize('100');
        $req->setUseHasNext('true');
        try {
            $count = 0;  // count how many orders successfully saved, and return this value.
            $resp = $this->taobao->execute($req, $this->session_code);
            $result = json_decode(json_encode($resp), true);
            if (isset($result['trades'])){
                foreach($result['trades']['trade'] as $trade) {
                    foreach ($this->expand_orders($trade) as $item) {
                        //在映射库表里面存在才入库
                        if (in_array($item['orders_num_iid'],array_keys($this->cache_map_tmall['map_goods']))){
                            $this->taobao_orders_model->updateOrCreate(['tid' => $item['tid']],  $this->cleanify_array($item));
                            // TODO: need to ensure that requires details
                            $this->taobao_order_detail($item['tid']);
                            // TODO: need to ensure that requires logistics
                            $this->logistics_dummy($item['tid']);
                            // TODO: 将订单写入到tmall线索
                            $this->clues_model->updateOrCreate(['ext_json' => $item['tid']],$this->cleanify_clues_array($item));
                            // TODO:判断订单是否支付,支付则发送卡券
                            if ($item['orders_status'] == 'WAIT_SELLER_SEND_GOODS') {
                                if (Cache::store('redis')->get('tmall:taobao:orders:tid' . $item['tid']) !== $item['tid']) {
                                    Cache::store('redis')->put('tmall:taobao:orders:tid' . $item['tid'], $item['tid'],3600);
                                    $this->send_cbb_card($item);
                                }

                            }
                            $count ++;
                        }

                    }
                }
                Log::debug(json_encode(['result' => 0, 'msg' => $count . ' orders saved.']));
                return response()->json(['result' => 0, 'msg' => $count . ' orders saved.']);
            }else{
                Log::debug('not has new orders');
            }

        } catch (\Exception $e) {
            // TODO check if error msg contains session_code has expired
            if (1 == 2) {
                $this->session_code = $this->update_taobao_session_code();
            }
            Log::error($e->getMessage());
        }
    }

    //发送车巴巴卡券
    public function send_cbb_card($param)
    {
        $cache_map = $this->cache_map_tmall;
        $data['mobile'] =  $param['receiver_address']; //电话
        $data['source'] = 3;
        $data['userName'] = $param['buyer_nick']; //姓名
        $data['cardId'] =$cache_map['map_card'][$param['orders_num_iid']] ; //卡券ID
        $data['activity_store_code'] = $cache_map['map_store'][$this->full_info['et_shop_id']]; //专营店
        $data['storeCodes'] =$cache_map['map_store'][$this->full_info['et_shop_id']]; //专营店
        $data['sign'] = $this->signCode($data);

        try {
            $client = new Client();
            $response = $client->request('POST', env('CHEBABA_API_COUPON_URI') . 'coupon' . '/' . 'common-coupon',['form_params'=>$data]);
            if ($response->getStatusCode() == 200) {
                $resp = json_decode($response->getBody()->getContents(), true);
                Log::debug(json_encode($resp));
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    private function signCode($params )
    {
        if (empty($params) || !is_array($params)) {
            return false;
        }
        ksort($params);
        $md5Str = '';
        foreach ($params as $key => $value) {
            $md5Str .= $value . '&';
        }
        $md5Str .= env('CHEBABA_API_COUPON_SING_KEY','ChEbAbA^&*_5439810@1');
        $singStr = substr(md5($md5Str), 6, 20); //从第5位开始，取20位
        return $singStr;
    }



    //拼装线索
    private function cleanify_clues_array($data)
    {
        $cache_map = $this->cache_map_tmall;
        $fields = [];
        if (!empty($data)){
            $fields['user_name'] = $data['buyer_nick'];
            $fields['phone'] = $data['receiver_address'];
            $fields['dealer_code'] = $cache_map['map_store'][$this->full_info['et_shop_id']];
            $fields['series_id'] = $cache_map['map_goods'][$data['orders_num_iid']];
            $fields['send_channel'] = 'CHEBABA';
            $fields['is_send'] = '1';
            $fields['activity_name'] = '双11测试活动';
            $fields['pageid'] = 'ABC123';
            $fields['smartcode'] = '';
            $fields['ext_json'] = $data['tid'];
        }
        return $fields;
    }

    //缓存天猫映射
    public function get_tmall_maps($ref=true)
    {
        if ($ref){
            $cache_store_map = $cache_goods_map = null;
        }else{
            $cache_store_map = Cache::store('redis')->get('tmall:maps:store');
            $cache_goods_map = Cache::store('redis')->get('tmall:maps:goods');
        }

        if (empty($cache_store_map)) {
            $store_map = [];
            $store_map_obj = DB::table('map_store')->select('tmall_store_id', 'store_id')->get();
            foreach ($store_map_obj as $store) {
                $store_map[$store->tmall_store_id] = $store->store_id;
            }
            $cache_store_map = $store_map;
            Cache::store('redis')->put('tmall:maps:store', $store_map, 60);
        }


        if (empty($cache_goods_map)) {
            $goods_map = [];
            $card_map = [];
            $goods_map_obj = DB::table('map_goods')->select('gc_id', 'goods_id','card_id')->get();
            foreach ($goods_map_obj as $goods) {
                $goods_map[$goods->goods_id] = $goods->gc_id;
                $card_map[$goods->goods_id] = $goods->card_id;
            }
            $cache_card_map = $card_map;
            $cache_goods_map = $goods_map;
            Cache::store('redis')->put('tmall:maps:goods', $goods_map, 60);
        }
        $cache_data =[
            'map_store' =>$cache_store_map,
            'map_goods' =>$cache_goods_map,
            'map_card' =>$cache_card_map
        ];
        return $cache_data;
    }

    //拼装order表数据
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

    //将数据写入到order_detail
    public function taobao_order_detail($tid) {
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
            $this->full_info = $trade;
            unset($trade['tid_str']);  // remove tid_str field which was not needed but forcely returned by Taobao!
            $return = $this->taobao_orders_detail_model->updateOrCreate(['tid' => $tid], $this->cleanify_array($trade));
            return response()->json(['result' => 0, 'msg' => '', 'data' => $return]);
        } catch (\Exception $e) {
            // TODO check if error msg contains session_code has expired
            if (1 == 2) {
                $this->session_code = $this->update_taobao_session_code();
            }
            Log::error($e);
        }
    }

    //订单自动发货状态
    public function logistics_dummy($tid) {
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
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    //去掉false的数据
    private function cleanify_array($data)
    {
        $result = [];
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if ($v === true) {
                    $v = 1;
                } else {
                    if ($v === false) {
                        $v = 0;
                    }
                }
                if (empty($v)) {
                    continue;
                }

                $result[$k] = $v;
            }
        }
        return $result;
    }

    //刷新sessio_code
    public function update_taobao_session_code() {
        $req = new TopAuthTokenRefreshRequest;
        $req->setRefreshToken($this->refresh_token);
        $result = null;

        try {
            $resp = $this->taobao->execute($req, $this->session_code);
            $result = json_decode(json_encode($resp), true);
            // TODO: test
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }



}
