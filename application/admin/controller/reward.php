<?php

namespace app\admin\controller;
use think\Page;

class Reward extends Base {

    

    public function list(){
    	$Ad =  M('reward_config');
    	$res = $Ad->select();
    	$this->assign('list',$list);// 赋值数据集
    	return $this->fetch();
    }
    
    public function rewardaction(){
    	$data = I('post.');
       // $data['topic_content'] = $_POST['topic_content']; // 这个内容不做转义
    	if($data['act'] == 'add'){
    		$data['ctime'] = time();
    		$find = db('topic')->where(['topic_title'=>$data['topic_title']])->find();
    		if($find){$this->ajaxReturn(['status'=>0,'msg'=>'已存在改标题','result'=>'']);}
    		$r = D('topic')->add($data);
    	}
    	if($data['act'] == 'edit'){
    	    $where['topic_title'] = $data['topic_title'];
    	    $where['topic_id'] = array('neq',$data['topic_id']);
            $find = db('topic')->where($where)->find();
            if($find){$this->ajaxReturn(['status'=>0,'msg'=>'已存在改标题','result'=>'']);}
    		$r = D('topic')->where('topic_id='.$data['topic_id'])->save($data);
    	}
    	 
    	if($data['act'] == 'del'){
    		$r = D('topic')->where('topic_id='.$data['topic_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	 
    	if($r !== false){
			$this->ajaxReturn(['status'=>1,'msg'=>'操作成功','result'=>'']);
    	}else{
			$this->ajaxReturn(['status'=>0,'msg'=>'操作失败','result'=>'']);
    	}
    }
}