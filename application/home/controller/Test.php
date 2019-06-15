<?php

namespace app\home\controller;

use think\Controller;
use think\Db;


/**
 * 测试
 */
class Test extends Controller{

    //更换 user_id
    public function index(){
        exit();
        
        $old_user_id = '16239111';
        $new_user_id = '17953394';

        $old_user = M('users')->where(['user_id'=>$old_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();
        echo '老用户';
        dump($old_user);

        $old_order = M('order')->where(['user_id'=>$old_user_id])->select();

        if($old_order){
            dump($old_order);
            exit('失败，老用户有订单');
        }

        $old_oauth_users = M('oauth_users')->where(['user_id'=>$old_user_id])->find();
        //dump($old_oauth_users);
        if($old_oauth_users){
            exit('存在授权');
        }

        $new_user = M('users')->where(['user_id'=>$new_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();
        
        echo '新用户';
        dump($new_user);

        $new_order = M('order')->where(['user_id'=>$new_user_id])->field('order_id,user_id')->select();
        if($new_order){
            dump($new_order);
        }

        //把订单的user_id改成old_user_id

        $new_oauth_users = M('oauth_users')->where(['user_id'=>$new_user_id])->find();
        //dump($new_oauth_users);
        if($new_oauth_users){

            dump($new_oauth_users);
            // exit('new存在授权');
        }


        //余额明细表：

        $account = M('account_log')->where(['user_id'=>$new_user_id])->select();
        if($account){
            dump($account);
        }
        

        $agent_performance = M('agent_performance')->where(['user_id'=>$new_user_id])->select();
        if($agent_performance){
            dump($agent_performance);
        }


        $agent_performance_log = M('agent_performance_log')->where(['user_id'=>$new_user_id])->select();
        if($agent_performance_log){
            dump($agent_performance_log);
        }


        $first_leader = M('users')->where(['first_leader'=>$first_leader])->select();

        // dump($first_leader);

        //$this->update($old_user_id,$new_user_id);
    }



    private function update($old_user_id,$new_user_id){

        $old_user = M('users')->where(['user_id'=>$old_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();

        $new_user = M('users')->where(['user_id'=>$new_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();

        //改ID
        if($old_user['level'] > $new_user['level']){
            M('users')->where(['user_id'=>$new_user_id])->update(['level'=>$old_user['level']]);
        }


        //删掉原来的user
        $res = M('users')->where(['user_id'=>$old_user_id])->delete();
        if ($res) {
            //把原来的id  给  新的
            M('users')->where(['openid'=>$new_user['openid']])->update(['user_id'=>$old_user_id]);

            M('order')->where(['user_id'=>$new_user_id])->update(['user_id'=>$old_user_id]);
        }


        
    }



}