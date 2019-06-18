<?php
/**
 * debug
 */
namespace app\api\controller;
use app\common\util\jwt\JWT;
use think\Db;
use think\Controller;

class Debug extends Controller
{

    public function tixian(){
        $id = I('id');
        if(!$id){
            echo "请输入ID";
            exit;
        }

        $res= M('withdrawals')->where(['id'=>$id])->find();
        if(!$res){
            echo "提现记录不存在";
            exit;
        }


        dump($res);

        if($res['status'] != -1){
            echo "不能更改";
            exit;
        }

        if($res['remark'] == ' ' || $res['remark'] == ''){
            echo "<a href='/api/debug/change?id=$id'>转成未审核</a>";
        }else{
            echo "这个已经处理了，备注是：".$res['remark'];
        }

    }

    public function change(){
        $id = I('id');
        if(!$id){
            echo "请输入ID";
            exit;
        }

        $res= M('withdrawals')->where(['id'=>$id])->find();
        if(!$res){
            echo "提现记录不存在";
            exit;
        }

        if($res['status'] != -1){
            echo "不能更改";
            exit;
        }


        if($res['remark'] == ' ' || $res['remark'] == ''){
            echo "<a href='/api/debug/change?id=$id'>转成未审核</a>";
        }else{
            M('withdrawals')->where(['id'=>$id])->update(['status'=>0]);
            $this->redirect('tixian');
        }


    }

   
}
