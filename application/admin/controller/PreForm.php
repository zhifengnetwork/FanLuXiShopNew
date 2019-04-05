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
            'od.pay_status'=>1,
            
          ];
         $order_goods = Db::name('order_goods')->alias('og')
             ->field('u.first_leader,sum(og.goods_num*og.goods_price) as sale_amount')
             ->where($where_goods)
              ->order('goods_id DESC')
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
    //触发发送奖金
    public function send_reward()
    {
       $list = Db::name('share')->order('grade','desc')->select(); //分红列表
    
     // $year = date('Y');
      //$season = $this->getQuarterDate($year,1);//第一季度
      //print_r($season);exit;
       $where_goods = [
            'od.order_status'=> ['notIN','3,5'],
           // 'og.is_send'    => 1,
            'og.prom_type' =>0,//只有普通订单才算业绩
            //'u.first_leader'=>$v['user_id'],
            //"og.goods_num" =>'>1',
           // 'od.user_id'=> ['IN',$user_s],
             'od.pay_status'=>1,
            
          ];
         $order_goods = Db::name('order_goods')->alias('og')
             ->field('u.first_leader,u.user_id,og.order_id,og.goods_num*og.goods_price as sale_amount')
             ->where($where_goods)->order('user_id DESC')
              ->join('order od','od.order_id=og.order_id','LEFT')
              ->join('users u','u.user_id=od.user_id','LEFT')
            // ->limit($Page->firstRow,$Page->listRows)
             ->select();
            //print_r($order_goods);exit;
            //print_r(Db::name('order_goods')->getlastsql());exit;
          $parent =array();
          $p_y  =array();
          $s_y  =array();
          foreach($order_goods as $k=>$v)
          {
            if(!empty($v['first_leader']))  //统计订单用户上级业绩
            {
            // echo $v['sale_amount'];
              $p_y[$v['first_leader']]['team_par']+= $v['sale_amount'];
              //查询该用户是否有上上级
              $p_parent = Db::name('users')->alias('u')
              ->where('user_id='.$v['first_leader'])
              ->field('u.user_id,u.first_leader')
              ->find();
              if(!empty($p_parent['first_leader']))
              {
                  //统计订单用户下级级业绩
                 $parent = Db::name('users')->alias('u')
                ->where('first_leader='.$v['first_leader'])
                ->field('u.user_id,u.first_leader')
                ->find();

              if(!empty($parent))
              {
                 // $s_y[$p_parent['first_leader']][$v['first_leader']]['team_par'][]= $v['sale_amount'];
                $s_y[$p_parent['first_leader']]['team_par']+= $v['sale_amount'];
                
              }
              }
            }
      
          }


         return $order_goods;
    }
    /*计算每个用户应该发的奖金*/

    public function count_userreward($user_id,$p_y,$s_y){
      if(!empty($p_y))
      {
        $commission = $goods['shop_price'] * ($fanli['rate'] / 100) * $this->goodNum;

        $bool = M('users')->where('user_id',$user_id)->setInc('user_money',$commission);
        if ($bool !== false) {
                $desc = "分享返利";
                $log = $this->writeLog($user_info['first_leader'],$commission,$desc,1); //写入日志
                  //检查返利管理津贴
                  $this->jintie($user_info['first_leader'],$commission);
                return true;
               } else {
                return false;
               }
      }else
      {
        return false;
      }


    }

    /*
 * 取某个季度的开始和结束时间
 *   $year 年份，如2014
 *   $season 季度，1、2、3、4
 */
  public function getQuarterDate($year,$season){
    $times = array();
    $times['st'] = date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,$year));
    $times['et'] = date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,$year)),$year));
    return $times;
  }
    
}