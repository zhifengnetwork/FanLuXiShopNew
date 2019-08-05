<?php


namespace app\cron\controller;

use think\Controller;
use think\Db;
use app\common\util\Exception;
use app\common\logic\UsersLogic;

class Test extends Controller{

    /**
     * 一个一个分红
     */
    public function fff(){

        $res = M('quarter_bonus2')->find();

        $data = array(
			'user_id'=>$res['user_id'],
			'user_money'=>$res['fenhong'],
			'change_time'=>time(),
			'desc'=>date('Y-m').'季度分红',
			'states'=>72
        );

        Db::startTrans();
        
        $bool = M('users')->where('user_id',$res['user_id'])->setInc('user_money',$res['fenhong']);

		if($bool){
            M('account_log')->insert($data);
          
            M('quarter_bonus2')->where(['user_id'=>$res['user_id']])->delete();
            echo $res['user_id'].$res['nickname']."SUCCESS";
            Db::commit(); 
		}else{

            echo $res['user_id'].$res['nickname']."FAIL";

            Db::rollback();
        }
        
        
    }



    public function nickname(){
      
        exit;
        
        set_time_limit(0);
        $quarter_bonus = M('quarter_bonus2');
        $data = $quarter_bonus->where(['flag'=>3])->order('team_per desc')->limit(100)->field('id,user_id')->select();

        $user = M('users');

        foreach ($data as $k => $v) {
            $nickname = $user->where('user_id='.$v['user_id'])->value('nickname');

            dump($nickname);
            $quarter_bonus->where(['id'=>$v['id']])->update(['nickname'=>$nickname,'flag'=>4]);
        }
    }



    public function level(){
        exit;
        
        set_time_limit(0);
        $quarter_bonus = M('quarter_bonus');
        $data = $quarter_bonus->where(['flag'=>2])->order('team_per desc')->limit(2000)->field('id,user_id')->select();

        $user = M('users');

        foreach ($data as $k => $v) {
            $level = $user->where('user_id='.$v['user_id'])->value('level');

            dump($level);
            $quarter_bonus->where(['id'=>$v['id']])->update(['level'=>$level,'flag'=>3]);
        }
    }




    public function fenhong(){
        set_time_limit(0);

        exit;

        $quarter_bonus = M('quarter_bonus');
        $data = $quarter_bonus->where(['flag'=>1])->order('team_per desc')->limit(200)->select();

        $user = M('users');
        $Share = M('share');

        foreach ($data as $k => $v) {
            //dump($v);

            $childlist = $user->where('first_leader='.$v['user_id'])->field('user_id')->select();
            //dump($childlist);
            $shengyu = (float)$v['money'];

            foreach ($childlist as $v1) {
                $chengyuan = $quarter_bonus->where(['user_id'=>$v1['user_id']])->value('money');
                $shengyu = $shengyu - (float)$chengyuan;
               // dump('减去：'.$chengyuan);
            }

            dump($v['user_id'].'剩余'.$shengyu.'元');
            $quarter_bonus->where(['id'=>$v['id']])->update(['fenhong'=>$shengyu,'flag'=>2]);
            
        }



    }



    public function bbb(){
        set_time_limit(0);
// 结束
exit();
        $quarter_bonus = M('quarter_bonus');
        $data = $quarter_bonus->where(['flag'=>0])->order('team_per desc')->limit(1000)->select();

        dump($data);

        $Share = M('Share');

        foreach($data as $k => $v){
            $rate = $Share->where(['lower'=>['elt',$v['team_per']],'upper'=>['egt',$v['team_per']]])->order('lower desc')->value('rate');
            $price = floor($v['team_per'] * $rate)/100; 

            $quarter_bonus->where(['id'=>$v['id']])->update(['rate'=>$rate,'money'=>$price,'flag'=>1]);

        }

    }


    public function aaa(){
        set_time_limit(0);
// 结束
exit();
        // M('order_copy')->where(['pay_status'=>0])->delete();

        $order = M('order')->field('order_id')->limit(1000)->select();

        foreach($order as $k => $v){

            agent_performance($v['order_id']);

            M('order')->where(['order_id'=>$v['order_id']])->delete();

        }

        $zong  = M('order')->count('order_id');

        dump("还有：".$zong);

    }



   
    public function eeeeeee(){


        set_time_limit(0);

        // 结束
        exit();


        $data = M('agent_performance_log')->field('performance_id,user_id,money')->limit(1000)->select();

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