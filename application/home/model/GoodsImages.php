<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/10
 * Time: 9:22
 */

namespace app\home\model;


class GoodsImages extends BaseModel
{
    public function Goods(){
        return $this->belongsTo('Goods');
    }
}