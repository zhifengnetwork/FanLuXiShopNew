<?php
/**
 * DC环球直供网络
 * ============================================================================
 *   分销、代理
 */

namespace app\common\logic;

use app\common\logic\LevelLogic;
use think\Model;
use think\Db;

/**
 * 返利类
 */
class FanliLogic extends Model
{

	private $userId;//用户id
	private $goodId;//商品id
	private $goodNum;//商品数量
	private $orderSn;//订单编号
	private $orderId;//订单id

	public function __construct($userId,  $goodId, $goodNum, $orderSn, $orderId)
	{	
		$this->userId = $userId;
		$this->goodId = $goodId;
		$this->goodNum = $goodNum;
		$this->orderSn = $orderSn;
		$this->orderId = $orderId;
		$this->tgoodsid = $this->catgoods();
	}

    //会员返利
	public function fanliModel()
	{


		$price = M('goods')->where(['goods_id'=>$this->goodId])->value('shop_price');
		//判断商品是否是活动商品
		$good = M('goods')
				->where('goods_id', $this->goodId)
				->field('is_distribut,is_agent')
                ->find();
        //查询会员当前等级
		$user_info = M('users')->where('user_id',$this->userId)->field('first_leader,level')->find();
		//查询上一级信息
		$parent_info = M('users')->where('user_id',$user_info['first_leader'])->field('level')->find();
        //判断是否特殊产品成为店主，则不走返利流程
        //用户购买后检查升级
		$this->checkuserlevel($this->userId,$this->orderId);
		//echo $this->goodId.'-'.$this->tgoodsid.'-'.$user_info['level'];exit;
        if($this->goodId==$this->tgoodsid && $user_info['level']<2)
        {
            $this->addhostmoney($user_id,$parent_info);
        }else
        {
	       
		    if( $user_info['level']==1 || $user_info['level']==2) //不是复购
		    {
		     if(empty($user_info['first_leader'])) return false;//如果没有上一级则返回
		   
	          //查询会员等级返利数据
	        if($parent_info['level']==1) return false; //上一级是普通会员则不反钱
	          $fanli = M('user_level')->where('level',$parent_info['level'])->field('rate')->find();

	         //计算返利金额
	           $goods = $this->goods();
	          $commission = $goods['shop_price'] * ($fanli['rate'] / 100) * $this->goodNum; //计算佣金
	         // print_R($goods['shop_price'].'-'.$this->goodNum.'-'.$fanli['rate']);exit;
	          //按上一级等级各自比例分享返利
	          $bool = M('users')->where('user_id',$user_info['first_leader'])->setInc('user_money',$commission);

	         if ($bool !== false) {
	        	$desc = "分享返利";
	        	$log = $this->writeLog($user_info['first_leader'],$commission,$desc,101); //写入日志

	        	//return true;
	         } else {
	        	return false;
	         }
		    }elseif($user_info['level']>=2) //是复购
		    {
	           $fanli = M('user_level')->where('level',$user_info['level'])->field('rate')->find();
	         //计算返利金额
	          $commission = $goods['shop_price'] * ($fanli['rate'] / 100) * $this->goodNum; //计算佣金
	          //按上一级等级各自比例分享返利
	          $bool = M('users')->where('user_id',$user_info['user_id'])->setInc('user_money',$commission);

	         if ($bool !== false) {
	        	$desc = "复购返利";
	        	$log = $this->writeLog($user_info['user_id'],$commission,$desc,101); //写入日志

	        	//return true;
	         } else {
	        	return false;
	         }
	        }
	
		
	    }
	
		

	}
	//会员升级
	public function checkuserlevel($user_id,$order_id)
	{
         //自动升级为vip，店主，总监，大区董事自动申请
		
		 //扫码登陆

		 $order = M('order')->where(['order_id'=>$this->orderId])->find();
		 $user_info = M('users')->where('user_id',$user_id)->field('first_leader,level,is_code')->find();
		if($user_info['is_code']==1 && $order['pay_status']==1 && $user_info['level']==1)//自动升级vip
		{
              $res = M('users')->where(['user_id'=>$user_id])->update(['level'=>2]);
              	$desc = "购买产品成为vip";
	        	$log = $this->writeLog($user_info['user_id'],'',$desc,101); //写入日志
		}
		else if($this->goodId==$this->tgoodsid  && $order['pay_status']==1)//自动升级店主
		{

			$res_s = M('users')->where(['user_id'=>$user_id])->update(['level'=>3]);
			$desc = "购买指定产品获得店主";
	        $log = $this->writeLog($user_info['user_id'],'398',$desc,101); //写入日志
	        if($res_s)
	        {
	        	$this->addhostmoney2();//产生店主获得金额和津贴
	        }
		}
		else if($user_info['level']==3)//自动升级总监
		{

			$num=M('users')->where(['first_leader'=>$user_id,'level'=>3])->count();
             if($num>=30)
             {
                  $res = M('users')->where(['user_id'=>$user_id])->update(['level'=>4]);
                  $desc = "直推店主30个成为总监";
	        	  $log = $this->writeLog($user_info['first_leader'],'',$desc,101); //写入日志
             }

		}



	}
    //推荐店主获得金额
	public function addhostmoney($user_id,$parent_info)
	{
		$user_info = M('users')->where('user_id',$user_id)->field('first_leader,level')->find();
       //只有店主，总监，大区董事推荐店主才能的到店主推荐金额
		 if( $parent_info['level']==4 || $parent_info['level']==5)//
		 {
		//计算返利金额
		  $fanli = M('user_level')->where('level',$parent_info['level'])->field('rate','reward')->find();
          $goods = $this->goods();
          $commission = $fanli['reward']; //计算金额

         // print_R($goods['shop_price'].'-'.$this->goodNum.'-'.$fanli['rate']);exit;
          //按上一级等级各自比例分享返利
          $bool = M('users')->where('user_id',$user_info['first_leader'])->setInc('user_money',$commission);

	         if ($bool !== false) {
	        	$desc = "推荐店主获得金额";
	        	$log = $this->writeLog($user_info['first_leader'],$commission,$desc,101); //写入日志

	        	return true;
	         } else {
	        	return false;
	         }
	     }else
	     {
            return false;
	     }

	}
	//获得管理津贴
	public  function jintie()
	{
		$goods = M('goods')->field("shop_price,cat_id")->where(['goods_id'=>$this->goodId])->find();
		return $goods;$goods = M('goods')->field("shop_price,cat_id")->where(['goods_id'=>$this->goodId])->find();
		return $goods;$goods = M('goods')->field("shop_price,cat_id")->where(['goods_id'=>$this->goodId])->find();
		return $goods;$goods = M('goods')->field("shop_price,cat_id")->where(['goods_id'=>$this->goodId])->find();
		return $goods;
	}
	 //总监，大区董事产生一个店主的金额和管理津贴
	public function addhostmoney2($user_id)
	{
		//查询会员当前等级
		$user_info = M('users')->where('user_id',$this->userId)->field('first_leader,level')->find();
		//查询上一级信息
		$parent_info = M('users')->where('user_id',$user_info['first_leader'])->field('level')->find();
		if($parent_info['level']==4 || $parent_info['level']==5)
		{
           $fanli = M('user_level')->where('level',$parent_info['level'])->field('chan')->find();
	         //计算返利金额
	       $commission = $fanli['chan']; //计算佣金
	          //按上一级等级各自比例分享返利
	       $bool = M('users')->where('user_id',$user_info['user_id'])->setInc('user_money',$commission);
		}

	}
	//记录日志
	public function writeLog($userId,$money,$desc,$states)
	{
		$data = array(
			'user_id'=>$userId,
			'user_money'=>$money,
			'change_time'=>time(),
			'desc'=>$desc,
			'order_sn'=>$this->orderSn,
			'order_id'=>$this->orderId,
			'states'=>$states
		);

		$bool = M('account_log')->insert($data);


		if($bool){

			//分钱记录
			$data = array(
				'order_id'=>$this->orderId,
				'user_id'=>$userId,
				'status'=>1,
				'goods_id'=>$this->goodId,
				'money'=>$money
			);
			M('order_divide')->add($data);
		
		}
		
		return $bool;
	}
	public function goods(){
		$goods = M('goods')->field("shop_price,cat_id")->where(['goods_id'=>$this->goodId])->find();
		return $goods;
	}
	//特殊产品
	public function catgoods()
	{
		$goods = M('goods')->field("goods_id")->where(['cat_id'=>8])->find();
		if(!$goods) return 0;
		return $goods['goods_id'];
	}

	
}