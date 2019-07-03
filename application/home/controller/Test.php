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
       exit;
       
        $ok_user_id = '13549585';//保留

        $delete_user_id = '17953568';//删掉

        // 要  老的 ，删掉  新的

     

        $delete_order = M('order')->where(['user_id'=>$delete_user_id])->select();

        if($delete_order){
            dump($delete_order);
            exit('失败，准备删除的用户有订单');
        }

        $delete_oauth_users = M('oauth_users')->where(['user_id'=>$delete_user_id])->find();
        //dump($old_oauth_users);
        if($delete_oauth_users){
            exit('准备删除的用户存在授权');
        }

        $delete_user = M('users')->where(['user_id'=>$delete_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();
        if(!$delete_user){
            $this->error('新ID信息不存在');
        }

        echo '新用户';
        dump($delete_user);
    

        $delete_order = M('order')->where(['user_id'=>$delete_user_id])->field('order_id,user_id')->select();
        if($delete_order){
            dump($delete_order);
        }

        //把订单的user_id改成ok_user_id

        $delete_oauth_users = M('oauth_users')->where(['user_id'=>$delete_user_id])->find();
        //dump($delete_oauth_users);
        if($delete_oauth_users){
            dump($delete_oauth_users);
            exit('new存在授权');
        }

        //余额明细表：

        $account = M('account_log')->where(['user_id'=>$delete_user_id])->select();
        if($account){
            dump($account);
        }
        

        $agent_performance = M('agent_performance')->where(['user_id'=>$delete_user_id])->select();
        if($agent_performance){
            dump($agent_performance);
        }


        $agent_performance_log = M('agent_performance_log')->where(['user_id'=>$delete_user_id])->select();
        if($agent_performance_log){
            dump($agent_performance_log);
        }


        $this->update($ok_user_id,$delete_user_id);
    }



    private function update($ok_user_id,$delete_user_id){

        $ok_user = M('users')->where(['user_id'=>$ok_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();

        $delete_user = M('users')->where(['user_id'=>$delete_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();

        $ok_user = M('users')->where(['user_id'=>$ok_user_id])->field('user_id,nickname,openid,old_openid,level,user_money,head_pic')->find();
        
       
        //上级
        $new_first_leader = M('users')->where(['user_id'=>$delete_user_id])->value('first_leader');
        if($new_first_leader && (int)$new_first_leader > 0){
        //更新上级到 老的 ID  里
        M('users')->where(['user_id'=>$ok_user_id])->update(['first_leader'=>$new_first_leader]);
        }


        //改 级别
        if($ok_user['level'] > $delete_user['level']){
            M('users')->where(['user_id'=>$ok_user_id])->update(['level'=>$delete_user['level']]);
        }


        //删掉原来的user
        $res = M('users')->where(['user_id'=>$delete_user_id])->delete();

        //if ($res) {
            //把原来的id  给  新的
            M('users')->where(['openid'=>$delete_user['openid']])->update(['user_id'=>$ok_user_id]);

            // 找出 该删除的 ，换成 ok 的
            M('order')->where(['user_id'=>$delete_user_id])->update(['user_id'=>$ok_user_id]);
        //}

        //改 oauth_users
        if($delete_user['openid']){
            M('oauth_users')->where(['openid'=>$delete_user['openid']])->update(['user_id'=>$ok_user_id]);
        }


    }

}