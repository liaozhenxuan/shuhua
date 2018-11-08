<?php

namespace App;

use App\Common;
use App\Forms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @desc Clues model
 * @author Tom 2017-07-20
 */
class Clues extends Model {
    protected $table = 'clues';

    // protected $fillable = [];
    protected $guarded = [];
    use SoftDeletes;

    /**
     * get clue bean detail
     * @author Tom 2017-09-07
     * @param mobile phone
     * @param page_id pageId
     */
    public function get_detail_by_mobile($mobile, $page_id=null) {
        return $this->where('phone', $mobile)
        ->when(
            $page_id, function($query) use ($page_id) {
                return $query->where('pageid', $page_id);
            }
        )
        ->orderBy('created_at', 'desc')
        ->first();
    }

    /**
     * get latest clue list
     * @author Tom 2017-09-07
     * @param minute recent minutes, default 1 minute
     * @param id specified record, without any other conditions
     */
    public function get_latest($minutes=1, $id=null) {
        if (!empty($id)) return $this::where('id',$id)->get();
        return $this->where('created_at', '>=', date('Y-m-d H:i:s', time() - $minutes * 60))->get();
    }

    /**
     * update remark field after processing the record
     * @author Tom 2017-09-07
     * @param id specified record
     * @param remark new data will be appened on existed
     */
    public function update_remark($id, $remark) {
        $clue = $this::findOrFail($id);
        if (empty($clue)) return null;
        $remark_old = empty($clue['remark']) ? [] : json_decode($clue['remark'], true);
        $clue->update(['remark' => json_encode(array_merge($remark_old,$remark))]);
    }

    /**
     * process sms: replace vars, and send it
     * @author Tom 2017-08-23
     * @param clue clue entity
     * @return null
     */
    public function process_sms($clue) {
        if (empty($clue)) {
            return null;
        }

        $clue = $clue->toArray();
        $forms = new Forms;
        $entity = $forms->get_by_custom_url($clue['custom_url']);

        if (empty($entity)) {
            return null;
        }
        $sms_content = $entity['sms_template'];
        // series
        $comm = new Common;
        if (strstr($sms_content, '{车系') && !empty($clue['series_id'])) {
            $chebaba = $comm->chebaba('goodsClass', 'getCarSeriesInfo', ['carSeriesId' => $clue['series_id']]);
            $gcName = $chebaba['carSeries']['gcName'];
            $sms_content = str_replace('{车系名称}', $gcName, $sms_content);
        }

        // model
        if (strstr($sms_content, '{车型') && !empty($clue['model_id'])) {
            $chebaba = $comm->chebaba('goodsCommon', 'getCarTypeInfo', ['carTypeId' => $clue['model_id']]);
            $goodsName = $chebaba['goodsName'];
            $sms_content = str_replace('{车型名称}', $goodsName, $sms_content);
        }

        // dealer
        if (strstr($sms_content, '{经销商') && !empty($clue['dealer_code'])) {
            $chebaba = $comm->chebaba('store', 'getDealerInfo', ['dealerId' => $clue['dealer_code']]);
            $dealer = $chebaba;
            $sms_content = str_replace('{经销商名称}', $dealer['storeName'], $sms_content);
            $sms_content = str_replace('{经销商编码}', $dealer['memberName'], $sms_content);
            $sms_content = str_replace('{经销商地址}', $dealer['storeAddress'], $sms_content);
            $sms_content = str_replace('{经销商电话}', $dealer['storeServiceTel'], $sms_content);
        }

        // customer
        if (strstr($sms_content, '{客户')) {
            $sms_content = str_replace('{客户姓名}', $clue['user_name'], $sms_content);
            $sms_content = str_replace('{客户手机}', $clue['phone'], $sms_content);
        }

        return empty($sms_content) ? null : $comm->wxchina($clue['phone'], $sms_content);
    }

}
