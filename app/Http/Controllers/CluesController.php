<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Clues;
use Illuminate\Http\Request;

/**
 * @desc clues controller
 * @author Tom 2017-07-20
 */
class CluesController extends Controller {
    private $clues;

    public function __construct() {
        $this->clues = new Clues;
    }

    /**
     * store a new record
     * @param request Request
     * @author Tom 2017-09-07, add Exceptions Tom 2017-09-08
     * @return true or error
     */
    public function store(Request $request) {
        try {
            $clues = new Clues;

            $clues->user_name = $request->input('user_name');
            if (empty($clues->user_name)) throw new Exception('user_name 字段不允许为空');

            $clues->gender = $request->input('gender');

            $clues->phone = $request->input('phone');
            if (empty($clues->phone)) throw new Exception('phone 字段不允许为空');

            $clues->dealer_code = $request->input('dealer_code');
            $clues->series_id = $request->input('series_id');
            $clues->model_id = $request->input('model_id');

            $clues->ip = $request->input('ip');
            if (empty($clues->ip)) $clues->ip = '0.0.0.0';

            $clues->send_channel = $request->input('send_channel');
            if (empty($clues->send_channel)) $clues->send_channel = 'CHEBABA';

            $clues->is_send = $request->input('is_send');
	    if ($clues->is_send === null) $clues->is_send = 1; // 如果不传入参数，默认不下发

            $clues->activity_name = $request->input('activity_name');
            if (empty($clues->activity_name)) $clues->activity_name = '常规通用留资';

            $clues->pageid = $request->input('pageid');
            if (empty($clues->pageid)) throw new Exception('pageid 字段不允许为空');

            $clues->custom_url = $request->input('custom_url');
            if (empty($clues->custom_url)) throw new Exception('custom_url 字段不允许为空');

            $clues->smartcode = $request->input('smartcode');
            $clues->ext_json = $request->input('ext_json');
            $clues->remark = $request->input('remark');

            if ($clues->save()) return response()->json(['result' => 0, 'msg' => null]);
            throw new Exception('Legally params with unexpected result of saving!');
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * get one by mobile
     * @param mobile mobile number
     * @return json
     */
    public function get_by_mobile(Request $request, $mobile) {
        try {
            $page_id = $request->input('page_id');
            return response()->json([
                'result' => 0,
                'msg' => null,
                'data' => $this->clues->get_detail_by_mobile($mobile, $page_id)
            ]);
        } catch (Exception $e) {
            Log::error($e);
        }
    }

}
