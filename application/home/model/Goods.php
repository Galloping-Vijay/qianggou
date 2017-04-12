<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 11:26
 */

namespace app\home\model;


class Goods extends BaseModel
{


    //自定义初始化
    protected static function init()
    {

    }

    // 设置数据表（不含前缀）
    //protected $name = 'goods';

    protected $pk = 'goods_id';
}