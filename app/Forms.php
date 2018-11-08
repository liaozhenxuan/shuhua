<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @desc Forms model
 * @author Tom 2017-07-25
 */
class Forms extends Model {
    protected $table = 'forms';

    // protected $fillable = [];
    protected $guarded = [];
    use SoftDeletes;

    public function get_by_custom_url($custom_url) {
        if (empty($custom_url)) {
            return null;
        }
        $result = $this->where('custom_url', $custom_url)->where('is_enable', '1')->first();

        // 查无结果时，返回默认form
        if (empty($result)) return null;
        $result = $result->toArray();
        // 起止时间范围判断：未开始的活动与过期的活动都返回默认form
        $now = time();
        if (!empty($result['start_date']) && $result['start_date'] > $now) {
            return null;
        }
        if (!empty($result['end_date']) && $result['end_date'] < $now) {
            return null;
        }
        return $result;
    }

}
