<?php


namespace app\cron\controller;

use think\Controller;
use think\Db;
use app\common\util\Exception;
use app\common\logic\UsersLogic;

class Test extends Controller{

   
    public function aaa(){


        set_time_limit(0);

        $data = M('agent_performance_log')->limit(1000)->select();

        dump($data);

        foreach ($data as $kk =>$vv) {
            $shangji = get_uper_user($vv['user_id']);

            $shang = $shangji['recUser'];

dump($shang);


            $order_amount = $vv['money'];

            if ($shang) {
                foreach ($shang as $k => $v) {
                    $cunzai = M('agent_performance')->where(['user_id'=>$v['user_id']])->find();
                    //存在
                    if ($cunzai) {
                        $data11['team_per'] = $cunzai['team_per'] + $order_amount;
                        $data11['update_time'] = date('Y-m-d H:i:s');
                        $res = M('agent_performance')->where(['user_id'=>$v['user_id']])->save($data11);
                    } else {
                        $data1['user_id'] =  $v['user_id'];
                        $data1['team_per'] =  $order_amount;
                        $data1['create_time'] = date('Y-m-d H:i:s');
                        $data1['update_time'] = date('Y-m-d H:i:s');
                        $res = M('agent_performance')->add($data1);
                    }
                }
            }

            //个人业绩

            $user_id = $vv['user_id'];


            unset($cunzai);
            $cunzai = M('agent_performance')->where(['user_id'=>$user_id])->find();

           
            //存在
            if($cunzai){
              
                $data_new['ind_per'] = $cunzai['ind_per'] + $order_amount;
                $data_new['update_time'] = date('Y-m-d H:i:s');
                $res = M('agent_performance')->where(['user_id'=>$user_id])->save($data_new);

            }else{
    
                 
        
                $data22['user_id'] =  $user_id;
                $data22['ind_per'] =  $order_amount;
                $data22['create_time'] = date('Y-m-d H:i:s');
                $data22['update_time'] = date('Y-m-d H:i:s');
                $res = M('agent_performance')->add($data22);

            }



            M('agent_performance_log')->where(['performance_id'=>$vv['performance_id']])->delete();
        }




    }


}