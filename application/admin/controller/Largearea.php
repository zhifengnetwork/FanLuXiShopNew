<?php


namespace app\admin\controller;

use app\admin\logic\OrderLogic;
use app\common\model\UserLabel;
use think\AjaxPage;
use think\console\command\make\Model;
use think\Page;
use think\Verify;
use think\Db;
use app\admin\logic\UsersLogic;
use app\common\logic\MessageTemplateLogic;
use app\common\logic\GoodsLogic;
use app\common\logic\MessageFactory;
use app\common\model\Withdrawals;
use app\common\model\Users;
use app\common\model\AgentInfo;
use think\Loader;

class Largearea extends Base {

    public function index(){
        return $this->fetch();
    }
    

    
    public function lists(){
      $where='u.level=4';
      $count = Db::name('users')->alias('u')->field('u.user_id')
             //->join('order od','u.first_leader=od.user_id','LEFT')
             ->where($where)->count();
             
       $Page = new Page($count,10);
        $users = Db::name('users')->alias('u')
             ->where($where)
             ->field('u.user_id,u.realname,u.mobile')
             ->order('user_id asc')
             ->limit($Page->firstRow,$Page->listRows)->select();
          $user_ids =array();
          $user_idcc =array();
         foreach($users as $k=>$v)
         {
            $userdata[$k] = Db::name('users')->where('first_leader='.$v['user_id'])->column('user_id');
          //  $v['team_par'] = $return;
            if(!empty($userdata[$k])) {

                $user_ids = implode(',',$userdata[$k]);
            }else
            {
              $user_ids ='';
            }
            $total = $this->jisuanyeji($user_ids,1,2); //查统计团队业绩
            $return['znum'] =  Db::name('users')->alias('u')->field('u.user_id')->where('first_leader='.$v['user_id'].' and u.level=4')->count();
            $return['first'] = !empty($total)?$total[0]['sale_amount']:0; 
            $return['realname'] =$v['realname'];
            $return['mobile'] =$v['mobile'];
             $znum['znum'] =$return['znum'];
            $new_list[]=$return;
           
         }
        $this->assign('new_list',$new_list);
        $this->assign('pager',$Page);
        $this->assign('p',I('p/d',1));
        $this->assign('page_size',$this->page_size);
   
        return $this->fetch();
    }
    //计算团队业绩--订单计算
    public function jisuanyeji($user_s=array(),$start_time,$end_time)
    {
          if(empty($user_s)) {
            return '';
          }else
          {
              $where_goods = [
              'od.order_status'=> ['notIN','3,5'],
             // 'og.is_send'    => 1,
              'og.prom_type' =>0,//只有普通订单才算业绩
              //'u.first_leader'=>$v['user_id'],
              //"og.goods_num" =>'>1',
              'od.user_id'=> ['IN',$user_s],
              
            ];
            $order_goods = Db::name('order_goods')->alias('og')
               ->field('u.first_leader,sum(og.goods_num*og.goods_price) as sale_amount')
               ->where($where_goods)->order('goods_id DESC')
                ->join('order od','od.order_id=og.order_id','LEFT')
                ->join('users u','u.user_id=od.user_id','LEFT')
              // ->limit($Page->firstRow,$Page->listRows)
               ->select();
              // print_r(Db::name('order_goods')->getlastsql());exit;
            return $order_goods;

          }
    }

    
}