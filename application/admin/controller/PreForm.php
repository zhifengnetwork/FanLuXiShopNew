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
    	$Ad =  M('users');
	    $p = $this->request->param('p');
    	$res = $Ad->order('user_id')->page($p.',10')->select();
        $level = M('user_level')->select();
        foreach($level as $k=>$v)
        {
            $level[$v['level']] = $v;
        }

        
    	if($res){
    		foreach ($res as $val){
    			$val['level_name'] = $level[$val['level']]['level_name'];
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);// 赋值数据集
    	$count = $Ad->count();// 查询满足要求的总记录数
    	$Page = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	$show = $Page->show();// 分页显示输出
	     $this->assign('pager',$Page);
    	$this->assign('page',$show);// 赋值分页输出
    	return $this->fetch();
    }
    
}