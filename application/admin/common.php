<?php

/**
 * 查询
 */
function get_withdrawals_weixin($id){
	$res = M('withdrawals')->where(['id'=>$id])->find();
	if($res['status'] == 0){
		return '';
	}
	if($res['bank_name'] != '微信'){
		return '';
	}

	$partner_trade_no = M('withdrawals_weixin')->where(['partner_trade_no'=>$id.$res['user_id']])->find();
	if(!$partner_trade_no){
		return "<span onclick='bufa(".$id.")'><font color='red'>到账失败</font><span>";
	}else{
		return "<font color='green'>".$partner_trade_no['result_code']."</font>";
	}

}

/**
 * 获取昵称
 */
function get_nickname($user_id){
	return M('users')->where(['user_id'=>$user_id])->value('nickname');
}

function get_agent_log($user_id){

	
	$agent_money = M('agent_performance')->where(['user_id'=>$user_id])->find();
	
	if(empty($agent_money)){
		$money = 0;
	}else{
		$money = $agent_money['ind_per']+$agent_money['agent_per'];
	}

	return $money;
}
function get_agent_user($first_leader){

	
	$first_leader_user = M('users')->where(['user_id'=>$first_leader])->find();
	if(empty($first_leader_user)){
		$name = "无";
	}else{
		if($first_leader_user['nickname'] == null)
		{
			$name = $first_leader_user['mobile'];
		}else{
			$name = $first_leader_user['nickname'];
		}
	}

	return $name;
}

/**
 * 管理员操作记录
 * @param $log_info string 记录信息
 */
function adminLog($log_info){
    $add['log_time'] = time();
    $add['admin_id'] = session('admin_id');
    $add['log_info'] = $log_info;
    $add['log_ip'] = request()->ip();
    $add['log_url'] = request()->baseUrl() ;
    M('admin_log')->add($add);
}


/**
 * 平台支出记录
 * @param $data
 */
function expenseLog($data){
	$data['addtime'] = time();
	$data['admin_id'] = session('admin_id');
	M('expense_log')->add($data);
}
 
function getAdminInfo($admin_id){
	return D('admin')->where("admin_id", $admin_id)->find();
}

 
/**
 * 面包屑导航  用于后台管理
 * 根据当前的控制器名称 和 action 方法
 */
function navigate_admin()
{            
    $navigate = include APP_PATH.'admin/conf/navigate.php';
    $location = strtolower('Admin/'.CONTROLLER_NAME);
    $arr = array(
        '后台首页'=>'javascript:void();',
        $navigate[$location]['name']=>'javascript:void();',
        $navigate[$location]['action'][ACTION_NAME]=>'javascript:void();',
    );
    return $arr;
}

/**
 * 导出excel
 * @param $strTable	表格内容
 * @param $filename 文件名
 */
function downloadExcel($strTable,$filename)
{
	header("Content-type: application/vnd.ms-excel");
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
	header('Expires:0');
	header('Pragma:public');
	echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 根据id获取地区名字
 * @param $regionId id
 */
function getRegionName($regionId){
    $data = M('region')->where(array('id'=>$regionId))->field('name')->find();
    return $data['name'];
}

function getMenuArr(){
	$menuArr = include APP_PATH.'admin/conf/menu.php';
	if(IS_SAAS == 1){
		foreach($menuArr as $k=>$val){
			foreach ($val['child'] as $j=>$v){
				foreach ($v['child'] as $s=>$son){
					if($GLOBALS['SAAS_CONFIG']['is_base_app'] != 1 && $son['admin_saas'] == 1){
						unset($menuArr[$k]['child'][$j]['child'][$s]);//过滤菜单
					}
				}
			}
		}
	}
	$act_list = session('act_list');
	if($act_list != 'all' && !empty($act_list)){
		$right = M('system_menu')->where("id in ($act_list)")->cache(true)->getField('right',true);
        $role_right = '';
		foreach ($right as $val){
			$role_right .= $val.',';
		}
		foreach($menuArr as $k=>$val){
			foreach ($val['child'] as $j=>$v){
				foreach ($v['child'] as $s=>$son){
					if(strpos($role_right,$son['op'].'@'.$son['act']) === false){
						unset($menuArr[$k]['child'][$j]['child'][$s]);//过滤菜单
					}
				}
			}
		}
		foreach ($menuArr as $mk=>$mr){
			foreach ($mr['child'] as $nk=>$nrr){
				if(empty($nrr['child'])){
					unset($menuArr[$mk]['child'][$nk]);
				}
			}
		}
	}
	return $menuArr;
}


function respose($res){
	exit(json_encode($res));
}