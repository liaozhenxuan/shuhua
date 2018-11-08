<?php

namespace App;

use Log;

class Common {
    /**
     * @desc send sms via xuanwu sms api
     * http://211.147.239.62:9050/cgi-bin/sendsms?username=tmall@fszx&password=abc123!!&to=13002001319&text=hitom&subid=&msgtype=4
     * @author Tom 2017-08-23
     * @return result array
     */

     public function do_get($url){
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
     }

     public function wxchina($mobile, $content) {
        return $this->do_get('http://211.147.239.62:9050/cgi-bin/sendsms?username=tmall@fszx&password=abc123!!&to=' . $mobile . '&text=' . urlencode(iconv("UTF-8","gbk//TRANSLIT",$content)) . '&subid=&msgtype=4');
    }

    /**
     * Chebaba request format in json
     * @author Tom 2017-07-19
     * @param body entityInfo value
     * @return array
     */
     private function chebaba_format($body) {
        return array(
            'tradeHead' => array(
                'tradeTime' => date('Y-m-d H:i:s'),
                'servSeq' => random_string(32)
            ),
            'tradeBody' => array(
                'entityInfo' => is_array($body) ? $body : json_decode($body, true)
            )
        );
    }

    /**
     * Chebaba query function
     * @author Tom 2017-07-20
     * @param request \Request
     * @param entry class name
     * @param function function name, default null
     * @return json
     */
     public function chebaba($entry, $function=null, $data=null) {
        $param = [
            'query' => [
                'data' => json_encode($this->chebaba_format($data))
            ],
            'debug' => false,
            'http_errors' => false,
            'connect_timeout' => 5
        ];

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', env('CHEBABA_API_BASE_URI') . $entry . '/' . $function, $param);

            if ($response->getStatusCode() == 200) {
                $resp = json_decode($response->getBody()->getContents(), true);

                if ($resp && $resp['tradeHead']['retnCode'] == '0') {
                    return $resp['tradeBody']['entityInfo'];
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public function post_chebaba($entry, $function=null, $data=null) {
        $param = [
            'query' => [
                'data' => json_encode($this->chebaba_format($data))
            ],
            'debug' => false,
            'http_errors' => false,
            'connect_timeout' => 5
        ];

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', env('CHEBABA_API_BASE_URI_V2') . $entry . '/' . $function, $param);

            if ($response->getStatusCode() == 200) {
                $resp = json_decode($response->getBody()->getContents(), true);

                if ($resp && $resp['tradeHead']['retnCode'] == '0') {
                    return $resp['tradeBody']['entityInfo'];
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

}
