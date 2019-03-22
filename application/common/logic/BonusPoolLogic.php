<?php

namespace app\common\logic;

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
		$good_ids = M('order_goods')->where('order_id', $order_id)->field('goods_id')->select();
		$user_id = M('order')->where('order_id', $order_id)->value('user_id');

		foreach ($good_ids as $key => $value) {
			$good = M('goods')->where('goods_id', $value['goods_id'])->where('cat_id', 1)->value('goods_id');
			if($good){
				$goods[] = $good;
			}
		}

		$num = count($goods);

		if($num <= 0) return false;

		$this->put_in($num);
		$this->write_log();
	}

	//将邮费放进奖金池
	public function put_in($num)
	{
		
	}

	public function write_log()
	{

	}
}