<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Forms;
use Illuminate\Http\Request;

/**
 * forms controller
 * @author Tom 2017-07-25
 */
class FormsController extends Controller {
    private $forms;

    public function __construct() {
        $this->forms = new Forms;
    }

    /**
     * get form detail with specified custom_url
     * @author Tom 2017-07-25
     * @param custom_url custom url field
     * @return json
     */
    public function get_by_custom_url(Request $request, $custom_url) {
        try {
            return response()->json(['result' => 0, 'msg' => 0, 'data' => $this->forms->get_by_custom_url(clean_param($custom_url))]);
        } catch (Exception $e) {
            Log::error($e);
        }
    }

}
