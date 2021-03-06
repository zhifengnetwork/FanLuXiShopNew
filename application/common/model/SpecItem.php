<?php

namespace app\common\model;
use app\common\util\TpshopException;
use think\Model;
class SpecItem extends Model {
    public function delete()
    {
        $id = $this->getAttr('id');
        $spec_goods_price = db('spec_goods_price')->whereOr('key', $id)->whereOr('key', 'LIKE', '%\_' . $id)->whereOr('key', 'LIKE', $id . '\_%')->find();
        if ($spec_goods_price) {
            $goods_name = db('goods')->where('goods_id', $spec_goods_price['goods_id'])->value('goods_name');
            throw new TpshopException('删除规格值', 0, ['status' => 0, 'msg' => $goods_name . $spec_goods_price['key_name'] . '在使用该规格，不能删除']);
        }
        return parent::delete(); // TODO: Change the autogenerated stub
    }
}
