<?php

/**
 * 谷歌搜索接口
 * User: dray
 * Date: 15/7/10
 * Time: 下午3:43
 */
class Google extends Base
{
    static function desc() {
        return "/google - Searches Google and send results.";
    }

    static function usage() {
        return array(
            "/google - Searches Google and send results."
        );
    }

    /**
     * 当命令满足的时候，执行的基础执行函数
     * @throws Exception
     */
    public function run() {
        CFun::echo_log("Google run 执行");

        //如果是需要回掉的请求
        if (empty($this->text)) {
            $this->set_reply();

            return;
        }

        $url  = "http://ajax.googleapis.com/ajax/services/search/web?";
        $data = array(
            'v' => '1.0',
            'q' => $this->text,
        );

        $res = CFun::post($url, $data, 'json', 'GET');
        CFun::echo_log("发送 Google 查询: res=%s", $res);

        $res_str = '';
        if (!isset($res['responseStatus']) || $res['responseStatus'] != 200) {
            $res_str = $res['responseDetails'];
        } else {
            foreach ($res['responseData']['results'] as $v) {
                $res_str = $res_str . $v['titleNoFormatting'] . ' - ' . ($v['unescapedUrl'] ? $v['unescapedUrl'] : $v['url']) . PHP_EOL;
            }
        }

        //回复消息
        $msg = Telegram::singleton()->send_message(array(
            'chat_id'             => $this->chat_id,
            'text'                => $res_str,
            'reply_to_message_id' => $this->msg_id,
        ));

        CFun::echo_log("发送信息: msg=%s", $msg);


    }
}
