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
    private $appId = '';    //appid
    private $rsaPrivateKey = '';    //私钥
    private $alipayrsaPublicKey = "";   //支付宝公钥
//    private $payer_name = "flxswkj@126.com";    //付款方账户
//    private $payer_name = "iuiecr5727@sandbox.com";    //付款方账户
    private $aop;
    public function __construct()
    {
//        //$str = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtlGar3+dxJic0ZbF6BjOoaNfE7in5Ozexi4U7IPTFfPPB3INATlNbyYVsyNVzRrAAp9AsgxkzW2r2VM6i8ue4I+I+mFdPsVS6BaDGOZhYei1tAVp+jsLbPQ1s4yJgDkJgFyIZ/2Z56uqGS33d1wGPtYG2zqLh9Avm3VvmstqeTPrirXDFakcXf773SpK6AN9WKNbThPPBV9BiYXUD+i/z46Wo7v+Mvqoz1kCUjavhCVxcNtixBuc4Cg7pXw8E+/o6V9w+IuIWwk93FsWYx7Ra71fII9gTSJBTUHEDBu6qjLsZGk/EvKB744NfQq8ToTJFTMFJPAyDRRBsMcyn3EMfwIDAQAB';
//        $str = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzMsY6XD/AtG+k2hgBGryDUdOiB0RW1mHxgqMyvHzbfBW9Tf1ZdqWXYr3evvsYLORTgQ7IhZoeC3BnyPktzJKRY4JJb7tPafCMNs0ucNW1lZ5/H4eLScggVhanNvygwnFnkk3CVZxtlV0wZwJYYRkMQaX4jZUZFbUe33TDBuq85+dDCznkG2jMO8PavqV4c9iX+uQUKH5CVvJK7RjRwjPw/EqP3xB1YJbTNRDtgYRz2OrHYkh7YjQ8EssIFoTp4A9bZWESLXLwOjJj/acuGV8HX46zHg55Cmbo/xw6ti+XIdEOJqq2jaJEzWNGNt03aJqbi6JqvcBowiUiSVUFiSoMQIDAQAB';
//        $config = [
////            'APPID' => 2019041663926129,
//            'APPID' => 2088102178079391,
//            //'rsaPrivateKey' => 'MIIEpQIBAAKCAQEA6nlSMja77DHkO3YMRY43ySw0KkPWY5/LUyvDSI2AH3lkJfsCMcEYcUpyJnczce6EPAVSgS+vZ9s6IWy3dzhphO7fw7DLhGjrJp+EV8ZKzA9g3VT2CsuyqJHSlgcSwGaZ5f8WWzFHfCnszgs5w0qIu+keX2Fz4qGybN8dFxAvCY+LPcLCahOFOmRmp1uw7bgn6UWKYBOJPkmVBOraun2hr3CLWu2PqKpIrKx5hWgZNX3LIYifUENz0VFAwDl4Flg3mLncy6Jsa4R0kSP6ehwAbYWk0ESSv5FiiA5SCJEHNmFgLWczEMGm48efs1t0YbMrwJWI0lun8a7PZ/AHLwm85wIDAQABAoIBAHBWVfo23QxJzwZqBXEhtTqOEiQZwlKS0ZB0jChrmvH5b/D+dMuvru1AdLZXL++rDfHPvvqkBQ7mKtCuzKuy/GMzK0QPpUI4Hkmv7XE8UMO5rnf8Z7E+bMd0rgcxNlu2DI/0ChsA3jXvxEPnfvJA+IfHJcUe5K21OM4Oi1psZ4zVLVl34mthLg/4oYkbxexg9INL0HAxEhC0I51PrIupnKCn5NJlcco591sxQEfv9yPWuexrgE41iJk5NNFWy9Y/mmrKvwYyqSmNplTYZtHGbf9Vm1uDkX2vBVbDg6Mz1qTvnds2cLJPYWXMG5ECx2QjPl2l9Cofg9qguOLM/NROlCkCgYEA+hymTabBpKbLvHatv3+Pbjhrg/qnW7ieFHctIoWf0t1ay4w179fREr4T4k4llmjiEvHNaOCzBrF81IrY640h+H2rkZ91+df1N03my72IWpIfkz+ezaDO0/DZxJFIrA14fA3BkxhFiEzC7Ghx+jeRNFCcKasvh/ex6l7BMazwYM0CgYEA7/5tBJ9qrwEafbV1cRD2MXM2G8o5QmuipTCt0GC2cTZhZfD9qHokWFpsoiGgp4nw9lxqRUj13niYLczO7K94jwDLFK1xqieOfXsU+V1lJwdng6SAQebVYeOJOkpB185/YhlVg1Sr7orGwhLCNV6C5zJ/NwXP+GZrdHxhJmSZBIMCgYEAsPPaGTA06qfzlvgkP0shkCqsrqiFBZidhv82WKlPhSGE3mPpuTHowqjmaoM9hqfX4u1elaf8IW0rUziU9jpY4XUQEKxQDJ7k5+betiD3OpUNb+FgGj1+d2Z8u9zKHKg/KQ2Wedp/P0qH0jinAw+TVP7/LV/m9fyhzJ6TcvDW9LUCgYEAtfK4iCasZR17Dg9CiIQJgpgMT6lTG+4qkv6C6FZKOy61TOoWBWMEpw93CLxh5mMIEl8iGoEkFpRrG14JCxxFVHWPgY+1ewEeYDeuQRfzllFgw0c2DcCJyfsNkOm3XXuqy57VXAoXh3QjGAPMxVVv/QQluntnnrVXhiq+JLNj5y0CgYEAtJe/n+mZBITtgCGyO0yNdaE3fI0I+cp7A/aD7FG8V81t5gPxKRNx5+nRySLR/MDZ4WeoIzNyuUckdWV51xPRtDa5QqLo7hgiyzlvdohywzozj6iY7lZimEMJbwK7c5yP64Gj9j3Qapvq6KLzh1q2pc5ILs0gkWEGnUvIhjV5G/8=',
//            'rsaPrivateKey' => 'MIIEpQIBAAKCAQEAzMsY6XD/AtG+k2hgBGryDUdOiB0RW1mHxgqMyvHzbfBW9Tf1ZdqWXYr3evvsYLORTgQ7IhZoeC3BnyPktzJKRY4JJb7tPafCMNs0ucNW1lZ5/H4eLScggVhanNvygwnFnkk3CVZxtlV0wZwJYYRkMQaX4jZUZFbUe33TDBuq85+dDCznkG2jMO8PavqV4c9iX+uQUKH5CVvJK7RjRwjPw/EqP3xB1YJbTNRDtgYRz2OrHYkh7YjQ8EssIFoTp4A9bZWESLXLwOjJj/acuGV8HX46zHg55Cmbo/xw6ti+XIdEOJqq2jaJEzWNGNt03aJqbi6JqvcBowiUiSVUFiSoMQIDAQABAoIBAQCLHkzGCCSz3ZgAux6+4YeczZvjixuHWsKJHhGWq9YaEPKBkon5rwwGb6i+uvRQnKtQvD8PYPmG6k5ltRRh/p/FsD82jQTMpXGdjsu6haAv6n7jrykAs1u5gjPL6v5LVhAQ/tuMVFTa8CJog38755vIUhpLaWza3MFrQoZj60euRsAGCX0MmFCDX3wOTc39mivqVBg7VvUSkid7cTw9lKHUb4X1Wlf2CtY+N2wXKi6TAO6dGjN1j7JdRF2/iY8BRKqEqVv0Y7pX7KGv4GQGZbfI46iEABdD63EVqxc9srz7s+Fwd0AzuARmsfvGmfkpk0LB6b8h2D0sdNFfZcwK99mBAoGBAPDAPT5/JbhGAD9QXMBZqaZCNsBjyL+3k5UofcqJqd5lX1bw3CTdOa98pmxbSLWcTGVWVgfm8rY02459Btb/1Vub8Gi74lzK1qnQCD/rhNd+OdOkbTKDjxfi2E1ygM7Np2UBY/dzHdKhygtMhH+slY5N9d1mMKZp6FAOCMn+QgJFAoGBANnDzuOvT/BHWblwE8INsI89CJEXCW5xV/40SwX0bhSxUa9lzwEi9Rt0GM4dwFE4qPNyOsfeLboNyVkSp0pkqvPMSVSa80Yi+XUx9SX5Hwgtk7O0kzirEJ5WTQ1K7af8/3ITDWMolC5I/mBlxMPzA647eXvDGXD0etuGzPuHgmL9AoGBAKeQRZAwEuLU5esrVcMTJP7w6waw7NJzRSb1zcegVTjTOa5baf3GdBXuHi8gwdSVep61np9VYOzskTv1TUNrQObH/GYJDx4il5INlxBWLlmGI11o/g/AfWHUo1QUNA68GJw2gYuC9ejabrO6iCYMyvMG0+9K5uWMpQLDId7lNrA5AoGBANNYY5G8UtP7G4yKxtI+IQ5TiN3vwrqKjIDtVaa43KF+mq2lHGuY9tOnMG/SlhT97ZMgWQYjjrOUr8agAPaFjxjAOqc/Hz4BsaPZUXiCOGjiewgdCAkmlSFm1q9A+jhgpbab4RFMj8wAEcmS243la7wMgtHnvuhOjnJCeOzhTl0tAoGAKqFaGhfqKe0H74ocIoyumVWMXdNsgRUf0NCpAA7UcUQqKXIaHPk0Tfgvy3Ebiz+Y99KZ4U1Df62QhoigGtIcpAeV2+IoljSuLc8w4kn62IRmPWrfBxNDSOXrOI04KyZl545neaZjyYJ5s+3uSRgTQSDOCdGGWs9JdPIrCjnuip8=',
//            'rsaPublicKey' => $str,
//        ];
//        $this->appId = $config['APPID'];//appid
//        $this->rsaPrivateKey = $config['rsaPrivateKey']; //私钥
//        $this->alipayrsaPublicKey=$config['rsaPublicKey'];//支付宝公钥
        //引入单笔转账sdk
        vendor('alipayaop.AopSdk');
    }

//    public function init_aop_config()
//    {
////        $this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
//        $this->aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
//        $this->aop->appId = $this->appId;
//        $this->aop->rsaPrivateKey = $this->rsaPrivateKey;
//        $this->aop->alipayrsaPublicKey=$this->alipayrsaPublicKey;
//        $this->aop->apiVersion = '1.0';
//        $this->aop->signType = 'RSA';
//        $this->aop->postCharset='UTF-8';
//        $this->aop->format='JSON';
//    }

//    /**
//     * 存取日志
//     */
//    private function transferLog($order_number,$pay_no,$pay_name,$amount)
//    {
//        $where['order_number'] = $order_number;
//        $find = Db::name('alipay_transfer_log')->where($where)->find();
//        if ($find){
//            return false;
//        }else{
//            $data['order_number'] = $order_number;
//            $data['pay_no'] = $pay_no;
//            $data['pay_name'] = $pay_name;
//            $data['amount'] = $amount;
//            $data['create_time'] = time();
//            $res = Db::name('alipay_transfer_log')->insert($data);
//            if ($res){
//                return true;
//            }else{
//                return false;
//            }
//        }
//    }

//    /**
//     * 修改日志
//     */
//    private function edit_transferLog($order_number,$result_code,$sub_msg)
//    {
//        $model = Db::name('alipay_transfer_log');
//        $where['order_number'] = $order_number;
//        $result = Db::name('alipay_transfer_log')->where($where)->order('create_time desc')->find();
//        if ($result_code == 10000)
//        {
//            $result['status'] = 1;
//            $sub_msg = 'success';
//        }else{
//            $result['status'] = 2;
//        }
//        $result['memo'] = $sub_msg;
//        $result['update_time'] = time();
//        Db::name('alipay_transfer_log')->save($result);
//    }

//    /**
//     * 查单接口
//     */
//    public function query($order_number)
//    {
////        $this->aop = new \AopClient ();
//        $aop = new \AopClient ();
//        //配置参数
////        $this->init_aop_config();
//
//        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
//        $aop->appId = '2019041663926129';
//        $aop->rsaPrivateKey = 'MIIEpQIBAAKCAQEAzMsY6XD/AtG+k2hgBGryDUdOiB0RW1mHxgqMyvHzbfBW9Tf1ZdqWXYr3evvsYLORTgQ7IhZoeC3BnyPktzJKRY4JJb7tPafCMNs0ucNW1lZ5/H4eLScggVhanNvygwnFnkk3CVZxtlV0wZwJYYRkMQaX4jZUZFbUe33TDBuq85+dDCznkG2jMO8PavqV4c9iX+uQUKH5CVvJK7RjRwjPw/EqP3xB1YJbTNRDtgYRz2OrHYkh7YjQ8EssIFoTp4A9bZWESLXLwOjJj/acuGV8HX46zHg55Cmbo/xw6ti+XIdEOJqq2jaJEzWNGNt03aJqbi6JqvcBowiUiSVUFiSoMQIDAQABAoIBAQCLHkzGCCSz3ZgAux6+4YeczZvjixuHWsKJHhGWq9YaEPKBkon5rwwGb6i+uvRQnKtQvD8PYPmG6k5ltRRh/p/FsD82jQTMpXGdjsu6haAv6n7jrykAs1u5gjPL6v5LVhAQ/tuMVFTa8CJog38755vIUhpLaWza3MFrQoZj60euRsAGCX0MmFCDX3wOTc39mivqVBg7VvUSkid7cTw9lKHUb4X1Wlf2CtY+N2wXKi6TAO6dGjN1j7JdRF2/iY8BRKqEqVv0Y7pX7KGv4GQGZbfI46iEABdD63EVqxc9srz7s+Fwd0AzuARmsfvGmfkpk0LB6b8h2D0sdNFfZcwK99mBAoGBAPDAPT5/JbhGAD9QXMBZqaZCNsBjyL+3k5UofcqJqd5lX1bw3CTdOa98pmxbSLWcTGVWVgfm8rY02459Btb/1Vub8Gi74lzK1qnQCD/rhNd+OdOkbTKDjxfi2E1ygM7Np2UBY/dzHdKhygtMhH+slY5N9d1mMKZp6FAOCMn+QgJFAoGBANnDzuOvT/BHWblwE8INsI89CJEXCW5xV/40SwX0bhSxUa9lzwEi9Rt0GM4dwFE4qPNyOsfeLboNyVkSp0pkqvPMSVSa80Yi+XUx9SX5Hwgtk7O0kzirEJ5WTQ1K7af8/3ITDWMolC5I/mBlxMPzA647eXvDGXD0etuGzPuHgmL9AoGBAKeQRZAwEuLU5esrVcMTJP7w6waw7NJzRSb1zcegVTjTOa5baf3GdBXuHi8gwdSVep61np9VYOzskTv1TUNrQObH/GYJDx4il5INlxBWLlmGI11o/g/AfWHUo1QUNA68GJw2gYuC9ejabrO6iCYMyvMG0+9K5uWMpQLDId7lNrA5AoGBANNYY5G8UtP7G4yKxtI+IQ5TiN3vwrqKjIDtVaa43KF+mq2lHGuY9tOnMG/SlhT97ZMgWQYjjrOUr8agAPaFjxjAOqc/Hz4BsaPZUXiCOGjiewgdCAkmlSFm1q9A+jhgpbab4RFMj8wAEcmS243la7wMgtHnvuhOjnJCeOzhTl0tAoGAKqFaGhfqKe0H74ocIoyumVWMXdNsgRUf0NCpAA7UcUQqKXIaHPk0Tfgvy3Ebiz+Y99KZ4U1Df62QhoigGtIcpAeV2+IoljSuLc8w4kn62IRmPWrfBxNDSOXrOI04KyZl545neaZjyYJ5s+3uSRgTQSDOCdGGWs9JdPIrCjnuip8=';
//        $aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzMsY6XD/AtG+k2hgBGryDUdOiB0RW1mHxgqMyvHzbfBW9Tf1ZdqWXYr3evvsYLORTgQ7IhZoeC3BnyPktzJKRY4JJb7tPafCMNs0ucNW1lZ5/H4eLScggVhanNvygwnFnkk3CVZxtlV0wZwJYYRkMQaX4jZUZFbUe33TDBuq85+dDCznkG2jMO8PavqV4c9iX+uQUKH5CVvJK7RjRwjPw/EqP3xB1YJbTNRDtgYRz2OrHYkh7YjQ8EssIFoTp4A9bZWESLXLwOjJj/acuGV8HX46zHg55Cmbo/xw6ti+XIdEOJqq2jaJEzWNGNt03aJqbi6JqvcBowiUiSVUFiSoMQIDAQAB';
//        $aop->apiVersion = '1.0';
//        $aop->signType = 'RSA2';
//        $aop->postCharset='utf-8';
//        $aop->format='JSON';
//
//        $request = new \AlipayFundTransOrderQueryRequest ();
//        $request->setBizContent("{" .
//            "\"out_biz_no\":\"".$order_number."\"" .
//            " }");
////        $result = $this->aop->execute ( $request);
//        $result = $aop->execute ($request);
//        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
//        $resultCode = $result->$responseNode->code;
//        if(!empty($resultCode)&&$resultCode == 10000){
//            $res_arr['code'] = '00';
//            $res_arr['data'] = $result;
//        } else {
//            $res_arr['code'] = '-1';
//        }
//        return $res_arr;
//    }

    public function index () {
        $res = $this->pay();
        dump($res);die;
    }


    public function pay () {
        $aop = new \AopClient ();
//        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        $aop->appId = '2088102178079391';
        $aop->rsaPrivateKey = 'MIIEpQIBAAKCAQEA6nlSMja77DHkO3YMRY43ySw0KkPWY5/LUyvDSI2AH3lkJfsCMcEYcUpyJnczce6EPAVSgS+vZ9s6IWy3dzhphO7fw7DLhGjrJp+EV8ZKzA9g3VT2CsuyqJHSlgcSwGaZ5f8WWzFHfCnszgs5w0qIu+keX2Fz4qGybN8dFxAvCY+LPcLCahOFOmRmp1uw7bgn6UWKYBOJPkmVBOraun2hr3CLWu2PqKpIrKx5hWgZNX3LIYifUENz0VFAwDl4Flg3mLncy6Jsa4R0kSP6ehwAbYWk0ESSv5FiiA5SCJEHNmFgLWczEMGm48efs1t0YbMrwJWI0lun8a7PZ/AHLwm85wIDAQABAoIBAHBWVfo23QxJzwZqBXEhtTqOEiQZwlKS0ZB0jChrmvH5b/D+dMuvru1AdLZXL++rDfHPvvqkBQ7mKtCuzKuy/GMzK0QPpUI4Hkmv7XE8UMO5rnf8Z7E+bMd0rgcxNlu2DI/0ChsA3jXvxEPnfvJA+IfHJcUe5K21OM4Oi1psZ4zVLVl34mthLg/4oYkbxexg9INL0HAxEhC0I51PrIupnKCn5NJlcco591sxQEfv9yPWuexrgE41iJk5NNFWy9Y/mmrKvwYyqSmNplTYZtHGbf9Vm1uDkX2vBVbDg6Mz1qTvnds2cLJPYWXMG5ECx2QjPl2l9Cofg9qguOLM/NROlCkCgYEA+hymTabBpKbLvHatv3+Pbjhrg/qnW7ieFHctIoWf0t1ay4w179fREr4T4k4llmjiEvHNaOCzBrF81IrY640h+H2rkZ91+df1N03my72IWpIfkz+ezaDO0/DZxJFIrA14fA3BkxhFiEzC7Ghx+jeRNFCcKasvh/ex6l7BMazwYM0CgYEA7/5tBJ9qrwEafbV1cRD2MXM2G8o5QmuipTCt0GC2cTZhZfD9qHokWFpsoiGgp4nw9lxqRUj13niYLczO7K94jwDLFK1xqieOfXsU+V1lJwdng6SAQebVYeOJOkpB185/YhlVg1Sr7orGwhLCNV6C5zJ/NwXP+GZrdHxhJmSZBIMCgYEAsPPaGTA06qfzlvgkP0shkCqsrqiFBZidhv82WKlPhSGE3mPpuTHowqjmaoM9hqfX4u1elaf8IW0rUziU9jpY4XUQEKxQDJ7k5+betiD3OpUNb+FgGj1+d2Z8u9zKHKg/KQ2Wedp/P0qH0jinAw+TVP7/LV/m9fyhzJ6TcvDW9LUCgYEAtfK4iCasZR17Dg9CiIQJgpgMT6lTG+4qkv6C6FZKOy61TOoWBWMEpw93CLxh5mMIEl8iGoEkFpRrG14JCxxFVHWPgY+1ewEeYDeuQRfzllFgw0c2DcCJyfsNkOm3XXuqy57VXAoXh3QjGAPMxVVv/QQluntnnrVXhiq+JLNj5y0CgYEAtJe/n+mZBITtgCGyO0yNdaE3fI0I+cp7A/aD7FG8V81t5gPxKRNx5+nRySLR/MDZ4WeoIzNyuUckdWV51xPRtDa5QqLo7hgiyzlvdohywzozj6iY7lZimEMJbwK7c5yP64Gj9j3Qapvq6KLzh1q2pc5ILs0gkWEGnUvIhjV5G/8=';
        $aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtlGar3+dxJic0ZbF6BjOoaNfE7in5Ozexi4U7IPTFfPPB3INATlNbyYVsyNVzRrAAp9AsgxkzW2r2VM6i8ue4I+I+mFdPsVS6BaDGOZhYei1tAVp+jsLbPQ1s4yJgDkJgFyIZ/2Z56uqGS33d1wGPtYG2zqLh9Avm3VvmstqeTPrirXDFakcXf773SpK6AN9WKNbThPPBV9BiYXUD+i/z46Wo7v+Mvqoz1kCUjavhCVxcNtixBuc4Cg7pXw8E+/o6V9w+IuIWwk93FsWYx7Ra71fII9gTSJBTUHEDBu6qjLsZGk/EvKB744NfQq8ToTJFTMFJPAyDRRBsMcyn3EMfwIDAQAB';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
//        $aop->timestamp='2019-04-24 19:07:50';
        $request = new \AlipayFundTransToaccountTransferRequest ();
        $order_num = 'no_13266503166';
        $request->setBizContent("{" .
            "\"out_biz_no\":\"$order_num\"," .
            "\"payee_type\":\"ALIPAY_LOGONID\"," .
            "\"payee_account\":\"13226785330\"," .
            "\"amount\":\"0.1\"," .
            "\"payer_show_name\":\"上海交通卡退款\"," .
            "\"payee_real_name\":\"沙箱环境\"," .
            "\"remark\":\"转账备注\"" .
            "}");
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            return $this->query($order_num);
        }
    }
}