<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 15:48
 */

namespace app\home\controller;


use think\Db;

class Goods extends Base
{
    public function index(){
        echo 'goods';
    }

    //商品详情页
    public function goodsInfo(){
        $goods_id=input("param.id/d");
        $goods =Db::name('Goods')->where("goods_id",$goods_id)->find();

        if(empty($goods) || ($goods['is_on_sale'] == 0)){
            $this->error('该商品已经下架',url('Index/prometeList'));
        }

        if($goods['brand_id']){
            $brand = Db('Brand')->where("id",$goods['brand_id'])->find();
            $goods['brand_name'] = $brand['name'];
        }

        //商品是否正在促销中
        if($goods['prom_type'] == 1)
        {
            $goods['flash_sale'] = get_goods_promotion($goods['goods_id']);
            $flash_sale = Db::name('flash_sale')->where("id", $goods['prom_id'])->find();
            $this->assign('flash_sale',$flash_sale);
        }
        Db::name('Goods')->where("goods_id", $goods_id)->update(array('click_count'=>$goods['click_count']+1 )); //统计点击数

        $this->assign('goods',$goods);
        return $this->fetch();

    }
}