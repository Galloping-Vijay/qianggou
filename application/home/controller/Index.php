<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 9:42
 */

namespace app\home\controller;

use think\Db;
use app\home\model\goods;
use think\Verify;

class Index extends Base
{
    public function index(){
        echo 'home';
    }

    //添加
    public function add(){
        $good = new goods();
        $good->cat_id=100;
        $good->goods_sn='TP0000130';
        $good->goods_name= '测试';
        $good->brand_id=0;
        $good->weight=0;
        $good->click_count=100;
        $good->store_count =10;
        $good->market_price=100;
        $good->shop_price=90;
        $good->keywords=0;
        $good->goods_remark='asadfsfdasf ';
        $good->goods_content='fasdfa ';
        $good->original_img='/public/upload/goods/2016/04-21/57187dd92a26f.jpg';
        $good->is_real=1;
        $good->is_on_sale=1;
        $good->is_free_shipping=1;
        $good->on_time= NOW_TIME;
        $good->sort='50';
        $good->is_recommend=0;
        $good->is_new=0;
        $good->is_hot=0;
        $good->last_update=NOW_TIME;
        $good->goods_type=32;
        $good->give_integral=0;
        $good->exchange_integral=0;
        $good->suppliers_id=0;
        $good->sales_sum=10;
        $good->prom_type=0;
        $good->prom_id=0;
        $good->commission='0.00';

        if($good->save())
            echo '保存成功!';
        else
            echo '失败';
    }

    //删除
    public function del(){
        $del_good=Goods::where('goods_id','=','144')->delete();

        if($del_good){
            echo '删除成功';
        }else{
            echo '操作失败';
        }

    }

    //抢购活动
    public function promoteList(){
        $goodsList = DB::query("select * from tp_goods as g inner join tp_flash_sale as f on g.goods_id = f.goods_id   where ".time()." > start_time  and ".time()." < end_time");
        $brandList = Db::name('brand')->Field('id,name,logo')->select();

        $this->assign('goodsList',$goodsList);
        $this->assign('brandList',$brandList);
        /*foreach ($goodsList as $k=>$v){
            echo $v['goods_id'];
            echo "<br/>";
        }*/
       // dump($goodsList);
       // exit();
        return $this->fetch();

    }

    //验证码
    public function verify(){
        $Verify = new Verify();
        $Verify->entry();
    }

    //打印一些测试数据
    public function ceshi (){
        //echo __PREFIX__;
    }


}