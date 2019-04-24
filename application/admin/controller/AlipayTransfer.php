<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2019/4/24
 * Time: 10:23
 */

namespace app\admin\controller;

use think\Db;
class AlipayTransfer extends Base
{
    private $appId;    //appid
    private $rsaPrivateKey;    //私钥
    private $alipayrsaPublicKey;   //支付宝公钥
    public function __construct()
    {
        $this->configPay();
        //引入单笔转账sdk
    }

    private function configPay () {
        $this->appId = '2019041663926129';
        $this->rsaPrivateKey = 'MIIEpQIBAAKCAQEA6nlSMja77DHkO3YMRY43ySw0KkPWY5/LUyvDSI2AH3lkJfsCMcEYcUpyJnczce6EPAVSgS+vZ9s6IWy3dzhphO7fw7DLhGjrJp+EV8ZKzA9g3VT2CsuyqJHSlgcSwGaZ5f8WWzFHfCnszgs5w0qIu+keX2Fz4qGybN8dFxAvCY+LPcLCahOFOmRmp1uw7bgn6UWKYBOJPkmVBOraun2hr3CLWu2PqKpIrKx5hWgZNX3LIYifUENz0VFAwDl4Flg3mLncy6Jsa4R0kSP6ehwAbYWk0ESSv5FiiA5SCJEHNmFgLWczEMGm48efs1t0YbMrwJWI0lun8a7PZ/AHLwm85wIDAQABAoIBAHBWVfo23QxJzwZqBXEhtTqOEiQZwlKS0ZB0jChrmvH5b/D+dMuvru1AdLZXL++rDfHPvvqkBQ7mKtCuzKuy/GMzK0QPpUI4Hkmv7XE8UMO5rnf8Z7E+bMd0rgcxNlu2DI/0ChsA3jXvxEPnfvJA+IfHJcUe5K21OM4Oi1psZ4zVLVl34mthLg/4oYkbxexg9INL0HAxEhC0I51PrIupnKCn5NJlcco591sxQEfv9yPWuexrgE41iJk5NNFWy9Y/mmrKvwYyqSmNplTYZtHGbf9Vm1uDkX2vBVbDg6Mz1qTvnds2cLJPYWXMG5ECx2QjPl2l9Cofg9qguOLM/NROlCkCgYEA+hymTabBpKbLvHatv3+Pbjhrg/qnW7ieFHctIoWf0t1ay4w179fREr4T4k4llmjiEvHNaOCzBrF81IrY640h+H2rkZ91+df1N03my72IWpIfkz+ezaDO0/DZxJFIrA14fA3BkxhFiEzC7Ghx+jeRNFCcKasvh/ex6l7BMazwYM0CgYEA7/5tBJ9qrwEafbV1cRD2MXM2G8o5QmuipTCt0GC2cTZhZfD9qHokWFpsoiGgp4nw9lxqRUj13niYLczO7K94jwDLFK1xqieOfXsU+V1lJwdng6SAQebVYeOJOkpB185/YhlVg1Sr7orGwhLCNV6C5zJ/NwXP+GZrdHxhJmSZBIMCgYEAsPPaGTA06qfzlvgkP0shkCqsrqiFBZidhv82WKlPhSGE3mPpuTHowqjmaoM9hqfX4u1elaf8IW0rUziU9jpY4XUQEKxQDJ7k5+betiD3OpUNb+FgGj1+d2Z8u9zKHKg/KQ2Wedp/P0qH0jinAw+TVP7/LV/m9fyhzJ6TcvDW9LUCgYEAtfK4iCasZR17Dg9CiIQJgpgMT6lTG+4qkv6C6FZKOy61TOoWBWMEpw93CLxh5mMIEl8iGoEkFpRrG14JCxxFVHWPgY+1ewEeYDeuQRfzllFgw0c2DcCJyfsNkOm3XXuqy57VXAoXh3QjGAPMxVVv/QQluntnnrVXhiq+JLNj5y0CgYEAtJe/n+mZBITtgCGyO0yNdaE3fI0I+cp7A/aD7FG8V81t5gPxKRNx5+nRySLR/MDZ4WeoIzNyuUckdWV51xPRtDa5QqLo7hgiyzlvdohywzozj6iY7lZimEMJbwK7c5yP64Gj9j3Qapvq6KLzh1q2pc5ILs0gkWEGnUvIhjV5G/8=';
        $this->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6nlSMja77DHkO3YMRY43ySw0KkPWY5/LUyvDSI2AH3lkJfsCMcEYcUpyJnczce6EPAVSgS+vZ9s6IWy3dzhphO7fw7DLhGjrJp+EV8ZKzA9g3VT2CsuyqJHSlgcSwGaZ5f8WWzFHfCnszgs5w0qIu+keX2Fz4qGybN8dFxAvCY+LPcLCahOFOmRmp1uw7bgn6UWKYBOJPkmVBOraun2hr3CLWu2PqKpIrKx5hWgZNX3LIYifUENz0VFAwDl4Flg3mLncy6Jsa4R0kSP6ehwAbYWk0ESSv5FiiA5SCJEHNmFgLWczEMGm48efs1t0YbMrwJWI0lun8a7PZ/AHLwm85wIDAQAB';
    }

    public function index () {
        $str = 'https://openapi.alipayaop.com/gateway.do?app_id=2019041663926129&version=1.0&format=json&sign_type=RSA2&method=alipayaop.fund.trans.toaccount.transfer&timestamp=2019-04-25+00%3A27%3A40&auth_token=&alipay_sdk=alipayaop-sdk-php-20180705&terminal_type=&terminal_info=&prod_code=&notify_url=&charset=UTF-8&app_auth_token=&sign=xp0jud1d1S%2BZ7bOFy6d6tppQV0blbRWi9adai%2BRLArfHg04C2QAFb3xT6BNkbPjor0OP8WmyMhPFj2fd2UfCmrGcGcPinUrBBxmwmNAFdcjPeI8E261Fuk8ML7cpPkoWRmCXM95Agwt6t6vZei1djzw96C3icqynvrZCwOGIDGKOMmfJWkLQf11PzuuVKGASeflOzHJNfK3jps2Hze0AJVRxToDS4C0VBeYZyVn3sCuyb%2BwzKv4HHZNKmOOgwQEvZj4YY%2Fs0xqyBs4DfZJcGoxBiKV6dB5agCXYHR9z%2BpqiF1MsHx%2FxH7xLWjZtReF4q%2F4WGJIWdk1dFACdkWiQkzw%3D%3D';
        $out_biz_no = time();
        $payee_account = '13226785330';
        $amount = 0.1;
        $payer_show_name = '上海交通卡退款';
        $payee_real_name = '沙箱环境';
        $remark = '沙箱环境';
        $res = $this->pay($out_biz_no,$payee_account,$amount,$payer_show_name,$payee_real_name,$remark);
        dump($res);die;
    }

    public function pay ($out_biz_no,$payee_account,$amount,$payer_show_name,$payee_real_name,$remark) {
        vendor('alipayaop.AopSdk');
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $aop->signType = 'RSA2';
        $request = new \AlipayFundTransToaccountTransferRequest ();
//        $request->setBizContent("{" .
//            "\"out_biz_no\":\"$out_biz_no\"," .
//            "\"payee_type\":\"ALIPAY_LOGONID\"," .
//            "\"payee_account\":\"$payee_account\"," .
//            "\"amount\":\"$amount\"," .
//            "\"payer_show_name\":\"$payer_show_name\"," .
//            "\"payee_real_name\":\"$payee_real_name\"," .
//            "\"remark\":\"$remark\"" .
//            "}");
        $request ->setBizContent("{" .
            "\"out_biz_no\":\"'".$out_biz_no."'\"," .
            "\"payee_type\":\"ALIPAY_LOGONID\"," .
            "\"payee_account\":\"'".$payee_account."'\"," .
            "\"amount\":\"'".$amount."'\"," .
            "\"payer_show_name\":\"墨家互娱\"," .
            "\"payee_real_name\":\"'".$payee_real_name."'\"," .
            "\"remark\":\"'".$remark."'\"" .
            "}");
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        dump($responseNode);
        $resultCode = $result->$responseNode->code;
        dump($resultCode);
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
}