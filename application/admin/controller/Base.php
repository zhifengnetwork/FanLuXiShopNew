<?php

namespace app\admin\controller;

use app\common\logic\Saas;
use think\Controller;
use think\Db;
use think\response\Json;
use think\Session;
class Base extends Controller {

    public $begin;
    public $end;
    public $page_size = 0;
    public $admin_id = 0;

    /**
     * 析构函数
     */
    function __construct() 
    {
        Session::start();
        header("Cache-control: private");  // history.back返回后输入框值丢失问题 参考文章 http://www.dchqzg1688.com/article_id_1465.html  http://blog.csdn.net/qinchaoguang123456/article/details/29852881
        parent::__construct();
     
        //用户中心面包屑导航
        $navigate_admin = navigate_admin();
        $this->assign('navigate_admin',$navigate_admin);
   }
    
    /**
     * 初始化操作
     */
    public function _initialize()
    {
        Saas::instance()->checkSso();

        //过滤不需要登陆的行为 
        if (!in_array(ACTION_NAME, array('login', 'vertify'))) {
            if (session('admin_id') > 0) {
                $this->check_priv();//检查管理员菜单操作权限
                $this->admin_id = session('admin_id');
            }else {
                (ACTION_NAME == 'index') && $this->redirect( U('Admin/Admin/login'));
                $this->error('请先登录', U('Admin/Admin/login'), null, 1);
            }
        }
        $this->public_assign();
    }

    /**
     * 保存公告变量到 smarty中 比如 导航 
     */
    public function public_assign()
    {
       $tpshop_config = array();

       $tp_config = M('config')->cache(true, TPSHOP_CACHE_TIME, 'config')->select();
       if($tp_config){
           foreach($tp_config as $k => $v)
           {
               $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
           }
       }
       
        if(I('start_time')){
            $begin = I('start_time');
            $end = I('end_time');
        }else{
            // $begin = date('Y-m-d', strtotime("-3 month"));//30天前
            $begin = '2019-4-10';
            $end = date('Y-m-d', strtotime('+1 days'));
        }
        $this->assign('start_time',$begin);
        $this->assign('end_time',$end);
        $this->begin = strtotime($begin);
        $this->end = strtotime($end);//+86399;
        $this->page_size = C('PAGESIZE');
       $this->assign('tpshop_config', $tpshop_config);
    }
    
    public function check_priv()
    {
    	$ctl = CONTROLLER_NAME;
    	$act = ACTION_NAME;
        $act_list = session('act_list');
		//无需验证的操作
		$uneed_check = array('login','logout','vertifyHandle','vertify','imageUp','upload','videoUp','delupload','login_task');
    	if($ctl == 'Index' || $act_list == 'all' || $ctl == 'Wx3rd'){
    		//后台首页控制器无需验证,超级管理员无需验证
    		return true;
    	}elseif((request()->isAjax() && $this->verifyAjaxRequest($act)) || strpos($act,'ajax')!== false || in_array($act,$uneed_check)){
    		//部分ajax请求不需要验证权限
    		return true;
    	}else{
            $res = $this->verifyAction();
    		if($res['status'] == -1){
                $this->error($res['msg'],$res['url']);
            };
    	}
    }
    
    public function ajaxReturn($data = [],$type = 'json'){
        //header('Content-Type:application/json; charset=utf-8');
        /*$data   = !empty($data) ? $data : ['status' => 1, 'msg' => '操作成功'];
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));*/
        exit(json_encode($data));
    }

    /**
     * 要验证的ajax
     * @param $act
     * @return bool
     */
    private function verifyAjaxRequest($act){
        $verifyAjaxArr = ['delGoodsCategory','delGoodsAttribute','delBrand','delGoods'];
        if(request()->isAjax() && in_array($act,$verifyAjaxArr)){
            $res = $this->verifyAction();
            if($res['status'] == -1){
                $this->ajaxReturn($res);
            }else{
                return true;
            };
        }else{
            return true;
        }
    }
    private function verifyAction(){
        if(IS_SAAS){
            return 1;
        }
        $ctl = CONTROLLER_NAME;
        $act = ACTION_NAME;
        $act_list = session('act_list');
        $right = M('system_menu')->where("id", "in", $act_list)->cache(true)->getField('right',true);
        $role_right = '';
        foreach ($right as $val){
            $role_right .= $val.',';
        }
        $role_right = explode(',', $role_right);
        //检查是否拥有此操作权限
        if(!in_array($ctl.'@'.$act, $role_right)){
            return ['status'=>-1,'msg'=>'您没有操作权限['.($ctl.'@'.$act).'],请联系超级管理员分配权限','url'=>U('Admin/Index/welcome')];
        }
    }
}