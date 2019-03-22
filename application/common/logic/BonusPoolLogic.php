<?php

namespace app\common\logic;

use app\common\model\Users;
use app\common\model\BonusRank;
use think\Model;
use think\Db;

/**
 * 活动逻辑类
 */
class BonusPoolLogic extends Model
{
	//判断商品是否为领取类目的产品
	public function is_receive($order_id)
	{
		$goods = array();
		//领取产品的用户
		$user_id = M('order')->where('order_id', $order_id)->value('user_id');
		$user = M('users')->where('user_id', $user_id)->field('user_id, nickname, first_leader')->find();

		//订单中的所有产品ID
		$good_ids = M('order_goods')->where('order_id', $order_id)->field('goods_id')->select();

		//获取领取类目的产品
		foreach ($good_ids as $key => $value) {
			$good = M('goods')->where('goods_id', $value['goods_id'])->where('cat_id', 1)->value('goods_id');
			if($good){
				$goods[] = $good;
			}
		}

		//计算产品数量
		$num = count($good_ids);
		if($num <= 0) return false;

		//开启事务
		Db::startTrans();
		$money = $this->put_in($num);
		
		if(!$money) return false;

		$log_result = $this->write_log($num, $money, $user, $order_id);

		if(!$log_result) return false;

		$rank_result = $this->write_ranking($num, $money, $user);
		
		if($rank_result){
			// 提交事务
            Db::commit();  
            return true;
		}else{
			// 回滚事务
            Db::rollback();
            return false;
		}
	}

	//将邮费放进奖金池
	public function put_in($num)
	{
		//获每个产品抽取的邮费
		$bonus_pool = M('config')->where('name', 'bonus_pool')->value('value');
		if(!$bonus_pool) return false;

		$money = $num * $bonus_pool;
		$bonus_total = M('config')->where('name', 'bonus_total')->value('value');
		$bonus_total = $bonus_total + $money;

		$result = Db::name('config')->where('name', 'bonus_total')->update(['value' => $bonus_total]);
		if($result){
			return $money;
		}else{
			return false;
		}
	}

	//记录领取日志
	public function write_log($num, $money, $user, $order_id)
	{
		$data = array(
			'user_id' => $user['user_id'],
			'user_name' => $user['nickname'],
			'leader_id' => $user['first_leader'],
			'order_id' => $order_id,
			'nums' => $num,
			'money' => $money,
			'create_time' => time(),
			'desc' => '领取'.$num.'个商品',
		);
		$result = Db::name('receive_log')->insert($data);
		return $result;	
	}

	//记录上级排名
	public function write_ranking($num, $money, $user)
	{
		//查询用户是否已经存在排名
		$users = M('bonus_rank')->where('user_id', $user['first_leader'])->find();

		//用户已存在排名则更新数据, 否则插入一条新记录
		if($users){
			$data = array(
				'nums' => $users['nums'] + $num,
				'money' => $users['money'] + $money,
				'update_time' => time(), 
			);
			$result = Db::name('bonus_rank')->insert($data);
			return $result;
		}else{
			$data = array(
				'user_id' => $user['first_leader'],
				'nums' => $num,
				'money' => $money,
				'create_time' => time(),
				'update_time' => time(),
			);
			$result = Db::name('bonus_rank')->insert($data);
			return $result;
		}
	}

	//奖金池奖励
	public function bonus_reward()
	{
		//查询排名奖励表是否已经奖励

		//判断奖金池金额是否不为0
		$bonus_total = M('config')->where('name', 'bonus_total')->value('value');
		if(!$bonus_total) return false;

		//按数量取最大的前四条记录
		Db::startTrans();
		$result = Db::name('bonus_rank')->order('nums DESC')->limit(4)->select();
		//截止时间,用于更新排名表时不更新后面
		$stop_time = time();
		if(!$result) return false;

		// //查询排名奖励比例
		$data = array('ranking1', 'ranking2', 'ranking3', 'ranking4');
		$rate = M('config')->where('name',['in', $data])->select();

		foreach ($rate as $key => $value) {
			$rates[] = $value['value']; 	
		}

		$i = 0;
		$log = array();
		$user = array();
		foreach ($result as $key => $value) {
			$money = ($rates[$i] / 100) * $bonus_total;
			$user_money = M('users')->where('user_id', $value['user_id'])->value('user_money');
			$result = Db::name('users')->where('user_id', $value['user_id'])->update(['user_money' => $money]);
			if(!$result){
				Db::rollback();
				return false;
			}
			// $user[$i]['user_id'] = $value['user_id'];
			// $user[$i]['user_money'] = $user_money + $money;

			$log[$i]['user_id'] = $value['user_id'];
			$log[$i]['money'] = $money;
			$log[$i]['rangking'] = $i + 1;
			$log[$i]['create_time'] = time();
			$log[$i]['desc'] = '奖金池排名奖励';

			$i++;
		}

		$result = Db::name('bonus_rank')->where('status', 0)->update(['status'=>1]);
		if($result){
			Db::commit();
			return true;
		}else{
			Db::rollback();
			return false;
		}

		// $log_result = M('bonus_log')->inset($log);

		// if(!$log_result && !$user_result){
		// 	M()->rollback();
		// 	return false;
		// }


	}
}