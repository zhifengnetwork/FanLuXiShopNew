<?php

namespace app\common\logic;

/**  
 * 模板消息类
 */
class TemplateMessage 
{

    /**
     * 佣金
     */
    public function yongjin($openid,$goods_name,$yongjin,$order_sn){

        $data = array(
            'touser'=> $openid,
            'template_id' => "58i9tVawAXWrwGWiLS5Csh-_PRVO5W4jAXc3qnQhWec",
            'url' => "https://www.wapdu.cn/shop/user/member.html",
            'data' => array(
                'first'=>array(
                    'value'=> "您分销的商品有买家已成功下单哦
订单编号：".$order_sn."
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $goods_name,
                    'color'=>"#FF0000"
                ),
                'keyword2'=>array(
                    'value'=> $yongjin,
                    'color'=>"#000000"
                ),
                'keyword3'=>array(
                    'value'=> '付款成功',
                    'color'=>"#000000"
                ),
                
                'remark'=>array(
                    'value'=>"
推广积分类型：VIP会员推广积分",
                    'color'=>"#172B6E"
                )
            )
        );

        $content = json_encode($data);
        $task_id = md5($content.$openid);
        $res = $this->send_tpl($openid,$task_id,$data);
        exit($res);
    }

    /**
     * 公共发送方法
     *
     *
     * 发送消息模板
     */
    public function send_tpl($openid, $task_id, $data)
    {
        // file_get_contents('https://www.wapdu.cn/api/token/access_token')
        $access_token = access_token();

        $res = M('template_message')->where(['task_id' => $task_id])->value('res');
        if ($res) {
            return $res;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($ch);
        curl_close($ch);

        $data = array(
            'task_id' =>$task_id,
            'content' => $json,
            'res' => $out,
        );

        M('template_message')->add($data);

        return $out;
    }





}