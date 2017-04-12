<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/7
 * Time: 15:24
 */

namespace app\home\controller;


use app\home\logic\Fpage;
use app\home\model\FlashSale;
use app\home\model\Goods;
use app\home\model\GoodsImages;
use app\home\model\Shop;

class Promotion extends Base
{
    //抢购活动
    public function flash_sale(){
        $goods = new FlashSale();
        $list = $goods->where('is_end',0)
                ->order('start_time DESC')
                ->paginate(3);
        $this->assign('list',$list);
        return $this->fetch();
    }

    //抢购详情页
    public function flash_sale_info(){
        $goodId=input('goods_id');

        $googImg = new GoodsImages();
        $imgList = $googImg->where('goods_id',$goodId)
            ->select();
        $good = FlashSale::get(array("goods_id" => $goodId));

        $shop =Shop::get(1);
        $this->assign('shop',$shop);
        $this->assign('imgList',$imgList);
        $this->assign('good',$good);
        return $this->fetch();
    }
}