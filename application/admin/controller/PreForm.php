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

class Preform extends Base {

    public function index(){
        return $this->fetch();
    }
    

    
    public function preform(){

      $count = Db::name('users')->alias('u')->field('u.user_id')
             //->join('order od','u.first_leader=od.user_id','LEFT')
             ->where($where)->count();

       $Page = new Page($count,10);
        $users = Db::name('users')->alias('u')
             ->field('u.user_id,u.realname,u.mobile')
             ->order('user_id asc')
             ->limit($Page->firstRow,$Page->listRows)->select();
          $user_ids =array();
          $user_idcc =array();
         foreach($users as $k=>$v)
         {
            $userdata = Db::name('users')->where('first_leader='.$v['user_id'])->column('user_id');
          //  $v['team_par'] = $return;
            if(!empty($userdata)) {
                $user_ids = implode(',',$userdata);
            }else
            {
              $user_ids='';
            }
            $total = $this->jisuanyeji($user_ids,1,2); //查统计团队业绩
            /*
            foreach($userdata as $ke=>$ve)
            {
                 $userdata_new = Db::name('users')->where('first_leader='.$v['user_id'])->column('user_id');
                  if(!empty($userdata_new)) {
                     $user_idcc = implode(',',$userdata_new);
                    }
                $return_new = $this->jisuanyeji($user_idcc,1,2); //查统计下下级团队业绩
            }*/
            
   
               // $return['first'] = !empty($total)?$total[0]['sale_amount']:0;//第一季度
            $return['first'] = !empty($total)?$total[0]['sale_amount']:0;

          //  $return['total'] = $total;
            $return['two'] = 1;//第二季度
            $return['thiree'] = 2;//第三季度
            $return['four'] = 3;//第四季度
            $return['k'] =$k;
            $return['realname'] =$v['realname'];
            $return['mobile'] =$v['mobile'];
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
          }
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

         return $order_goods;

    }

    public function checklog()
    {
        $start_time = strtotime(0);
        $end_time = time();
        if(IS_POST){
            $start_time = strtotime(I('start_time'));
            $end_time = strtotime(I('end_time'));
        }
        $count = M('fan_log')->alias('acount')->join('users', 'users.user_id = acount.user_id')
                    ->whereTime('acount.change_time', 'between', [$start_time, $end_time])
                   // ->where("acount.states = 101 or acount.states = 102")
                    ->count();
        $page = new Page($count, 10);
        $log = M('fan_log')->alias('acount')->join('users', 'users.user_id = acount.user_id')
                               ->field('users.nickname, acount.*')->order('log_id DESC')
                               ->whereTime('acount.change_time', 'between', [$start_time, $end_time])
                               //->where("acount.states = 101 or acount.states = 102")
                               ->limit($page->firstRow, $page->listRows)
                               ->select();
        
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('pager', $page);
        $this->assign('log',$log);
        return $this->fetch();
    }
    
}