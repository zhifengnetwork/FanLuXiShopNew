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

         write_log('凡露希微信-第一次公众号');
        $data = file_get_contents("php://input");
    	if ($data) {
    		$re = $this->xmlToArray($data);
            write_log('凡露希微信-第一yi次公众号'.json_encode($re));
	    	$url = SITE_URL.'/mobile/message/index?eventkey='.$re['EventKey'].'&openid='.$re['FromUserName'].'&event='.$re['Event'];
	    	httpRequest($url);
        }

        $config = Db::name('wx_user')->find();
        if ($config['wait_access'] == 0) {
           write_log('凡露希微信-第一次公众号11---'.$config['wait_access']);
            ob_clean();
            exit($_GET["echostr"]);
        }
        write_log('凡露希微信-第一次公众号22');
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