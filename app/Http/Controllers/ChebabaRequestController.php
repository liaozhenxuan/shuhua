<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Common;
use Illuminate\Http\Request;

/**
 * Chebaba Api Proxy
 * @author Tom 2017-07-19
 */
class ChebabaRequestController extends Controller {
    public function chebaba(Request $request, $entry, $function = null) {
        try {
            $comm = new Common;
            return response()->json(['result' => 0, 'msg' => null, 'data' => $comm->chebaba($entry, $function, $request->input('data'))]);
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public function city(){
        $comm = new Common;
        $areas = $comm->chebaba('area', 'getAreaByAreaDeep', ['areaDeep' => 1]);
        $cities = $comm->chebaba('area', 'getAreaByAreaDeep', ['areaDeep' => 2]);
        $newArea = [];
        
        foreach($areas as $key=>$val){
            $newArea[$val['regionalismCode']] = $val;
            foreach($cities as $k=>$v){
                if($val['areaId'] == $v['areaParentId']){
                    $newArea[$val['regionalismCode']]['city'][$v['regionalismCode']] = $v;
                    unset($cities[$k]);
                }
            }
            
        }
        return response()->json(['result'=>0,'msg'=>null,'data'=>$newArea]);
    }

}
