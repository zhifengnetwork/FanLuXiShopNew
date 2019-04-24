<?php


namespace app\home\controller;

use app\common\logic\WechatLogic;
use think\Db;

class Weixin
{
    /**
     * 处理接收推送消息
     */
    public function index()
    {


       write_log('凡露希微信推送消息5555');
       Db::name('wxlog')->add(['text'=>'露希微信推送消息5555']);
        $data = file_get_contents("php://input");
    	if ($data) {
    		$re = $this->xmlToArray($data);
           // $re =json_encode($re);
            write_log('凡露希微信推送消息'.json_encode($re));

	    	$url = SITE_URL.'/mobile/message/index?eventkey='.$re['EventKey'].'&openid='.$re['FromUserName'].'&event='.$re['Event'];
	    	httpRequest($url);
        }

        $config = Db::name('wx_user')->find();
        if ($config['wait_access'] == 0) {
            ob_clean();
            exit($_GET["echostr"]);
        }
        $logic = new WechatLogic($config);
        $logic->handleMessage();
    }


    public function xmlToArray($xml)
    {
    	$obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($obj);
		$arr = json_decode($json, true);  
		return $arr;
    }
    
}