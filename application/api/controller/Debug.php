<?php
/**
 * 签到API
 */
namespace app\api\controller;
use app\common\model\Users;
use think\Db;
use think\Controller;


class Debug extends Controller
{


    public function aa(){

        // $list = M('users')->group('openid')->limit(10)->field('user_id,openid,count(user_id) as num')->where(['num',2])->select();

        // select openid,count(*) as count from tp_users  WHERE openid!='' group by openid having count>1;

        $list = Db::query("select openid,count(*) as count from tp_users  WHERE openid!='' group by openid having count > 1 limit 1") ;

        $openid = $list[0]['openid'];

        $users = M('users')->where(['openid'=>$openid])->field('user_id,openid,old_openid,user_money,first_leader,nickname,head_pic')->select();

        foreach($users as $k => $v){
            $user_id = $v['user_id'];

            $v['order_num'] = M('order')->where(['user_id'=>$v['user_id']])->count();

            $v['uuuuuu_id'] = M('oauth_users')->where(['user_id'=>$user_id])->value('user_id');

            dump($v);
            echo "<img src=".$v['head_pic'].">";
            echo "<a href=/api/debug/del?user_id=".$user_id.">删除这个</a>";
        }

    }

    public function del(){
        $user_id = I('user_id');

        if($user_id){
            M('users')->where(['user_id'=>$user_id])->delete();
            M('oauth_users')->where(['user_id'=>$user_id])->delete();
        }

        $this->redirect('aa');
    }



    

    public function dddd()
    {
      
        $user_id = 17951598;


        $user = M('users')->where(['user_id'=>$user_id])->find();

        dump($user);


        $use = M('oauth_users')->where(['user_id'=>$user_id])->find();

        dump($use);


        // $user = M('users')->where(['user_id'=>$user_id])->delete();

        // $use = M('oauth_users')->where(['user_id'=>$user_id])->delete();



    }



}
