<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/12
 * Time: 15:12
 */

namespace app\admin\model;


class Access extends BaseModel
{
    public function getMenuAttr($value)
    {
        $menu = [0=>'禁用',1=>'一级',2=>'二级'];
        return $menu[$value];
    }

    public function getParentAttr($value,$data){

        //获取一级菜单
        $pid=$this->where('pid',0)->column('name','id');
        $pid['0'] = '一级菜单';
        return $pid[$data['pid']];
    }
}