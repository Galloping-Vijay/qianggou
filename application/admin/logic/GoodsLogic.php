<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/1
 * Time: 15:27
 */

namespace app\admin\logic;


use app\admin\model\GoodsAttribute;
use app\admin\model\GoodsCategory;
use think\Model;
use think\Db;

class GoodsLogic extends Model
{
    /**
     *  获取排好序的品牌列表
     */
    function getSortBrands()
    {
        $brandList =  Db::name("Brand")->select();
        $brandIdArr =  Db::name("Brand")->where("name in (select `name` from `".Config('database.prefix')."brand` group by name having COUNT(id) > 1)")->getField('id,cat_id');
        $goodsCategoryArr = Db::name('goodsCategory')->where("level = 1")->getField('id,name');
        $nameList = array();
        foreach($brandList as $k => $v)
        {

            $name = getFirstCharter($v['name']) .'  --   '. $v['name']; // 前面加上拼音首字母

            if(array_key_exists($v[id],$brandIdArr) && $v[cat_id]) // 如果有双重品牌的 则加上分类名称
                $name .= ' ( '. $goodsCategoryArr[$v[cat_id]] . ' ) ';

            $nameList[] = $v['name'] = $name;
            $brandList[$k] = $v;
        }
        array_multisort($nameList,SORT_STRING,SORT_ASC,$brandList);

        return $brandList;
    }
}