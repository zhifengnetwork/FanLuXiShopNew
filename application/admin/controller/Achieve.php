<?php

namespace app\admin\controller;

use app\admin\logic\GoodsLogic;
use app\common\logic\ModuleLogic;
use think\db;
use think\Cache;
use think\Page;

class Achieve extends Base
{
	public function config()
    {
        
        $config = tpCache('achieve');
        $config['averanking1'] = unserialize($config['averanking1']);
        $config['averanking2'] = unserialize($config['averanking2']);
        //print_R($config);exit;
        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }
    public function config_set()
    {
        if($_POST){

        $pool_open  = input('pool/s');
        //$averanking1 = input('averanking1');
        //$averanking2 = input('averanking2');
        $avetime1 = input('avetime1');
        $avetime2 = input('avetime2');
        $averanking = !empty($_POST['averanking'])?$_POST['averanking']:'';
        $averanking1 = !empty($_POST['averanking1'])?serialize($_POST['averanking1']):'';
        $averanking2 = !empty($_POST['averanking2'])?serialize($_POST['averanking2']):'';

            $data = array(
                'achieve_pool' => $pool_open,
                'averanking1' =>$averanking1,
                'averanking' =>$averanking,
                'averanking2' =>$averanking2,
                'avetime1' =>$avetime1,
                'avetime2' =>$avetime2,
                // 'day' =>$day,
                );
            foreach($data as $k =>$v){
                $updata = Db::query("update tp_config set value='$v' where inc_type='achieve' and name='$k'");
            }
            //delFile(RUNTIME_PATH);
            clearCache();
            $this->success("操作成功");

        }

    }
    public function slist(){

        $config = tpCache('achieve');
        $config['averanking1'] = unserialize($config['averanking1']);
        $config['averanking2'] = unserialize($config['averanking2']);

        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;//当天结算时间
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));//当天开始时间
        //exit('数据正在调整');
        $y_day =  date("Y-m-d",strtotime("-1 day")); //昨天
        $day = date("Y-m-d");
        $y_daytime = strtotime($y_day.$config['avetime1'].':00:00');
        $daytime =   strtotime($day.$config['avetime2'].':00:00');
        $where = " UNIX_TIMESTAMP(create_time)>=".$y_daytime.' and UNIX_TIMESTAMP(create_time)<='.$daytime." and u.level in(4,5) ";

        if ($_POST) {
            $user_id = input('user_id/s');
            //$where.= " and  b.user_id like '%$user_id%' ";
            //$cwhere. = "tp_users.user_id like '%$user_id%' ";
        }
       
        //$count = Db::table("tp_users")->join('tp_agent_performance',' tp_users.user_id=tp_agent_performance.user_id')->where($cwhere)->count();
        $count = M('agent_performance_log')->alias('a')
        ->field("create_time,sum(a.money) as new_money")
        ->join('tp_users u','a.user_id=u.user_id')
        ->where($where)
        ->having('new_money>='.$config['averanking'])
        ->group('a.user_id')
        

        ->count();
        //echo $count;exit;
        //echo  M('agent_performance_log')->getlastsql();exit;
       // $this->page_size=10;
        $Page = new Page($count,10);

        //$Pickup =  Db::query("select * from tp_agent_performance as a,tp_users as b where a.user_id = b.user_id $where order by a.team_per desc limit $page->firstRow,$page->listRows");
        // dump($page->firstRow.'='.$page->listRows);
        $Pickup = M('agent_performance_log')->alias('a')
         ->field("create_time,sum(a.money) as new_money,a.order_id,u.nickname,u.level,u.user_id")
        ->join('tp_users u','a.user_id=u.user_id')
        //->join('tp_account_log ac','ac.user_id=u.user_id')
        ->where($where)
        ->having('new_money>='.$config['averanking'])
        ->group('a.user_id')
        ->limit($Page->firstRow,$Page->listRows)
        ->order('new_money desc')
        
        ->select();
        //print_R(M('agent_performance_log')->getlastsql());exit;
        foreach ($Pickup as $key=>$val)
        {  $log_where = " change_time>=".$beginToday.' and change_time<='.$endToday.' and log_type=-1 and user_id='.$val['user_id'];
            $log = M('account_log')->where($log_where)->find();
            $Pickup[$key]['p_money']= $log['user_money'];
            $Pickup[$key]['change_time']= !empty($log['change_time'])?date("Y-m-d H:i:s",$log['change_time']):'';
            $Pickup[$key]['log_desc']= $log['desc'];

        }

        // $res = $Pickup->order('order_id desc')->select();
        $this->assign('page',$Page);
        $this->assign('list',$Pickup);

        return $this->fetch();
    }
    //自动发放业绩分红
    public function count_sent()
    {
        $config = tpCache('achieve'); //读取分红设置
        $config['averanking1'] = unserialize($config['averanking1']);
        $config['averanking2'] = unserialize($config['averanking2']);
        if($config['achieve_pool']==0)
        {
            exit("已关闭分红");
        }
        if(!$config)
        {
            exit("参数错误");
        }
        //$config['averanking1'] = unserialize($config['averanking1']);
       // $config['averanking2'] = unserialize($config['averanking2']);
        

        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $y_day =  date("Y-m-d",strtotime("-1 day")); //昨天
        $day = date("Y-m-d");
        $y_daytime = strtotime($y_day.$config['avetime1'].':00:00');
        $daytime =   strtotime($day.$config['avetime2'].':00:00');
        $where = " UNIX_TIMESTAMP(create_time)>=".$y_daytime.' and UNIX_TIMESTAMP(create_time)<='.$daytime." and u.level in(4,5)";

        //检查是否当天已发放
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;//当天结算时间
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));//当天开始时间
         $log_where = " change_time>=".$beginToday.' and change_time<='.$endToday.' and log_type=10';
         $log = M('account_log')->where($log_where)->find();
        // print_r(M('account_log')->getlastsql());exit;
         if($log)
         {
            exit("当天已经分红了");
         }

         $Pickup = M('agent_performance_log')->alias('a')
         ->field("create_time,sum(a.money) as new_money,a.order_id,u.nickname,u.level,u.user_id")
        ->join('tp_users u','a.user_id=u.user_id')
        //->join('tp_account_log ac','ac.user_id=u.user_id')
        ->where($where)
        ->having('new_money>='.$config['averanking'])
        ->group('a.user_id')
        ->order('new_money desc')
        ->select();  //统计当日业绩符合分红的会员

        foreach($Pickup as $key=>$val)
        {
            //开始分红
           if($val['level']==4)
           {
              $commission = $val['new_money']* ($config['averanking1']['b'][0] / 100);//计算业绩
          }elseif ($val['level']==5) {
             $commission = $val['new_money']* ($config['averanking2']['b'][0] / 100);//计算业绩
          }
           
                   
           
             //发放业绩分红，记录
              if(!empty($commission))
              {
               // $bool = M('users')->where('user_id',$val['user_id'])->setInc('user_money',$commission);
                  if ($bool !== false) {
                    $desc = "业绩分红";
                   
                    $log = $this->writeLog_user($val['user_id'],$commission,$desc,-1); //写入日志
                   } 

              }
        }
        exit('分红成功');
    }


        //记录日志
  public function writeLog_user($userId,$money,$desc,$states)
  {
    $data = array(
      'user_id'=>$userId,
      'user_money'=>$money,
      'change_time'=>time(),
      'desc'=>$desc,
      //'order_sn'=>$this->orderSn,
      //'order_id'=>$this->orderId,
      'log_type'=>$states
    );

    $bool = M('account_log_new')->insert($data);



    
    return $bool;
  }
}