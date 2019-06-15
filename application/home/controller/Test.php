<?php

namespace app\home\controller;

use think\Db;

/**
 * 测试
 */
class Test {

    //更换 user_id
    public function index(){
        $old_user_id = '16239111';
        $new_user_id = '17953394';

        // $old_user = M('users')->where(['user_id'=>$old_user_id])->field('user_id,nickname,openid,level,user_money,head_pic')->find();

        // $new_user = M('users')->where(['user_id'=>$new_user_id])->field('user_id,nickname,openid,level,user_money,head_pic')->find();

        // echo '老用户';
        // dump($old_user);

        // echo '新用户';
        // dump($new_user);

    }



}