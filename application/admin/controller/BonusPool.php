<?php

namespace app\admin\controller;

use think\Page;
use think\Db;

class BonusPool extends Base {
    /**
     * 奖金池排名
     */
    public function ranking()
    {
        return $this->fetch();
    }

    /**
     * 领取产品日志
     */
    public function receive_log()
    {
        return $this->fetch();
    }
}