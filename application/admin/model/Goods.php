<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/1
 * Time: 15:41
 */

namespace app\admin\model;


class Goods extends BaseModel
{
    // 定义关联模型列表
    protected static $relationModel = ['GoodsImages'];

    public function goods_category()
    {
        return $this->belongsTo('GoodsCategory');
    }


}