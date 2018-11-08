<?php

namespace App\Http\Controllers;

use App\Clues;
use App\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendTmallCluesToChebabaController extends Controller
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendTmallCluesToChebaba';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '下发天猫线索给车巴巴';

    private $clues;
    private $common;
    private $minutes;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->clues = new Clues;
        $this->common = new Common;
        $this->minutes = 1;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function to_chebaba(Request $request, $minutes=1)
    {
        if (isset($_GET['minute'])){
            $minutes = $_GET['minute'];
        }
        $clues = $this->clues->get_latest($minutes);
        if (empty($clues)) {
            return;
        }

        $cbbCount = 0;
        $smsCount = 0;
        foreach ($clues as $clue) {

            if (empty($clue)) {
                continue;
            }
            $remark = json_decode($clue['remark'], true);

            if (empty($remark['cbb'])) {
                $result = $this->send_to_chebaba($clue);
                if ($result['clueIds'][0]) {
                    $this->clues->update_remark($clue['id'], ['cbb' => $result['clueIds'][0]]);
                }
                $cbbCount++;
            }

            /*if (!isset($remark['sms']) || $remark['sms'] == -1) {
                $result = $this->send_clue_sms($clue);
                $this->clues->update_remark($clue['id'], ['sms' => $result]);
                $smsCount++;
            }*/
        }

        return response()->json([
            'error' => 0,
            'msg' => '',
            'data' => [
                'cbb' => $cbbCount,
                //'sms' => $smsCount
            ]
        ]);
    }

    protected function send_to_chebaba($data)
    {
        try {
            $array = [];
            $array['userName'] = isset($data['user_name']) ? $data['user_name'] : '天猫用户';
            $array['phone'] = isset($data['phone']) ? $data['phone'] : null;
            $array['storeCodes'] = isset($data['dealer_code']) ? $data['dealer_code'] : null;
            $array['pageId'] = isset($data['pageid']) ? $data['pageid'] : null;
            $array['clueName'] = isset($data['activity_name']) ? $data['activity_name'] : '常规通用留资';
            $array['carSeriesId'] = isset($data['series_id']) ? $data['series_id'] : null;
            $array['smartCode'] = isset($data['smartcode']) ? $data['smartcode'] : null;
            $array['source'] = '10';
            $array['terminal'] = 'wap';
            $array['ip'] = isset($data['ip']) ? $data['ip'] : '0.0.0.0';
            $array['clueUrl'] = isset($data['clueUrl']) ? $data['clueUrl'] : 'https://dongfeng-nissan.tmall.com';
            $array['clueType'] = '6';
            $array['is_send'] = isset($data['is_send']) ? $data['is_send'] : 0;
            $array['sex'] = isset($data['gender']) ? $data['gender'] : null;
            $array['carTypeId'] = isset($data['model_id']) ? $data['model_id'] : null;
            $array['infoSourceCodeD'] = '31237';
            return $this->common->post_chebaba('clue', 'handle', $array);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    protected function send_clue_sms($data)
    {
        return $this->clues->process_sms($data);
    }

    public function clues_process($minutes = 1, $id = null)
    {
        $clues = $this->clues->get_latest($minutes, $id);
        if (empty($clues)) {
            return;
        }

        $cbbCount = 0;
        $smsCount = 0;
        foreach ($clues as $clue) {

            if (empty($clue)) {
                continue;
            }
            $remark = json_decode($clue['remark'], true);

            if (empty($remark['cbb'])) {
                $result = $this->send_to_chebaba($clue);
                if ($result['clueIds'][0]) {
                    $this->clues->update_remark($clue['id'], ['cbb' => $result['clueIds'][0]]);
                }
                $cbbCount++;
            }

            /*if (!isset($remark['sms']) || $remark['sms'] == -1) {
                $result = $this->send_clue_sms($clue);
                $this->clues->update_remark($clue['id'], ['sms' => $result]);
                $smsCount++;
            }*/
        }
        $data = [
            'error' => 0,
            'msg' => '',
            'data' => [
                'cbb' => $cbbCount,
               // 'sms' => $smsCount
            ]
        ];
        return response()->json($data);
    }
}
