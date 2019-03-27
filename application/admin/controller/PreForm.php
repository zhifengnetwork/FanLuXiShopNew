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

      // $users = M('users')->where(['user_id'=>$post['user_id'],'agent_user'=>$post['level']])->find();
        /*
         $where =" 1 and od.order_id is not null ";
         $count = Db::name('users')->alias('u')->field('u.user_id')
             ->join('order od','u.first_leader=od.user_id','LEFT')
             ->group('u.user_id')
             ->where($where)->count();
         $Page = new Page($count,$this->page_size);
         $users = Db::name('users')->alias('u')
             ->field('u.user_id')
             ->join('order od','u.first_leader=od.user_id','LEFT')
             ->where($where)->order('user_id DESC')
             ->group('u.user_id')
             ->limit($Page->firstRow,$Page->listRows)->select();*/

         //$new_list = array();
      $count = Db::name('users')->alias('u')->field('u.user_id')
             //->join('order od','u.first_leader=od.user_id','LEFT')
             ->where($where)->count();
       $Page = new Page($count,$this->page_size);
        $users = Db::name('users')->alias('u')
             ->field('u.user_id,u.realname,u.mobile')
             ->order('user_id asc')
             ->limit($Page->firstRow,$Page->listRows)->select();
         foreach($users as $k=>$v)
         {
            
          //  $v['team_par'] = $return;
            $return = $this->jisuanyeji($v['user_id'],1,2); //查统计团队业绩
            foreach($return as $key=>$val)
            {
                $sale_amount+= $val['sale_amount'];
                
            }
            
            $return['first'] = $sale_amount;//第一季度
            $return['two'] = 1;//第二季度
            $return['thiree'] = 2;//第三季度
            $return['four'] = 3;//第四季度
            $return['k'] =$k;
            $return['realname'] =$v['realname'];
            $return['mobile'] =$v['mobile'];
            $new_list[]=$return;
            $sale_amount=0;

           
         }

        
        $this->assign('new_list',$new_list);
        $this->assign('pager',$Page);
        $this->assign('p',I('p/d',1));
        $this->assign('page_size',$this->page_size);
   
        return $this->fetch();
    }
    //计算团队业绩
    public function jisuanyeji($user_id,$start_time,$end_time)
    {

        $goods_name = I('goods_name');
        $where = [
           // 'od.pay_time'    => ['Between',"$this->begin,$this->end"],
            'od.order_status'=> ['notIN','3,5'],
           // 'og.is_send'    => 1,
            'og.prom_type' =>0,//只有普通订单才算业绩
            'u.first_leader'=>$user_id,
            //"og.goods_num" =>'>1',
        ];
        //if(!empty($goods_name))$where['og.goods_name'] =['like', "%$goods_name%"];
        // $count = Db::name('order_goods')->alias('og')
            // ->field('sum(og.goods_num) as sale_num,sum(og.goods_num*og.goods_price) as sale_amount ')
            // ->join('order od','og.order_id=od.order_id','LEFT')
            // ->where($where)->group('od.user_id')->count();
         //$Page = new Page($count,$this->page_size);
         $res = Db::name('order_goods')->alias('og')
             ->field('og.goods_name,og.goods_id,og.goods_sn,od.user_id,sum(og.goods_num) as sale_num,sum(og.goods_num*og.goods_price) as sale_amount,u.user_id,u.first_leader')
             ->join('order od','og.order_id=od.order_id','LEFT')
             ->join('users u','u.user_id=od.user_id','LEFT')
             ->where($where)->group('od.user_id')->order('goods_id DESC')
            // ->limit($Page->firstRow,$Page->listRows)
             ->select();
        //echo $user_id;exit;
  
         return $res;

    }
    //记录会员升级，返利，推荐店主等
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