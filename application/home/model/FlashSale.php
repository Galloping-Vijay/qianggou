<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/7
 * Time: 16:45
 */

namespace app\home\model;


class FlashSale extends BaseModel
{
    public function Goods()
    {
        return $this->belongsTo('Goods','goods_id');
    }
}