<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/1
 * Time: 10:38
 */

namespace app\admin\controller;


use app\admin\model\FlashSale;
use app\admin\model\Goods;
use think\Db;
use think\Request;

class Promotion extends Base
{
    //活动列表
    public function index(){
        echo "活动列表";

    }

    //抢购活动
    public function flash_sale(){
        //echo  '抢购活动';
        $condition=array();
        $model= new FlashSale();
        $count = $model->where($condition)->count();
        $list = $model->where($condition)->order("id desc")->paginate(10,$count);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //编辑抢购
    public function flash_sale_info(){
        $flash_sale =new FlashSale();
        $good = new Goods();

        if(Request::instance()->isPost()){
           $data = input('param.');
           $data['start_time'] = strtotime($data['start_time']);
           $data['end_time'] = strtotime($data['end_time']);

           if(empty($data['id'])){//添加抢购
               $r = $flash_sale::create($data);
               $good->where("goods_id=".$data['goods_id'])
                   ->update(array("prom_id"=>$r['id'],"prom_type"=>1));
           }else{//编辑抢购
               $r = $flash_sale->where("id=".$data['id'])
                   ->update($data);
               $good->where("goods_id=" . $data['goods_id'])
                   ->update(array('prom_id' => $data['id'], 'prom_type' => 1));
            }
            if($r){
               $this->success('编辑抢购活动成功', Url('Promotion/flash_sale'));
            }else{
                $this->error('编辑抢购活动失败', Url('Promotion/flash_sale'));
            }
        }
        $id=input('id');
        $info['start_time'] =date('Y-m-d H:i:s');
        $info['end_time'] =date('Y-m-d 23:59:59',time()+3600*24*60);

        if($id>0){
            $info=$flash_sale->where("id=$id")->find();
            $info['start_time'] =date('Y-m-d H:i', $info['start_time']);
            $info['end_time'] = date('Y-m-d H:i', $info['end_time']);
        }else{
            $info['goods_id']='';
            $info['id']='';
            $info['title']='';
            $info['goods_name']='';
            $info['price']='';
            $info['goods_num']='';
            $info['buy_limit']='';
            $info['description']='';
            $info['start_time'] =date('Y-m-d H:i', time());
            $info['end_time'] = date('Y-m-d H:i', time());
        }
        $this->assign('info',$info);
        $this->assign('min_date',date('Y-m-d'));

        return $this->fetch();
    }

    //删除
    public function flash_sale_del(){
        $id=input('param.id');
        FlashSale::destroy($id);
    }

    //团购管理
    public function group_buy_list(){
        echo '团购管理';
    }

    //优惠促销
    public function prom_goods_list(){
        echo '优惠促销';
    }

    //优惠券
    public function coupons(){
        echo '优惠券';
    }

    //搜索商品
    public function search_goods(){
        $condition ='is_on_sale = 1 and prom_type=0 and store_count>0';
        $goods=new Goods();
        $goodsList = $goods->where($condition)
            ->order('goods_id','desc')
            ->paginate(10);

        $count = $goods->count();

        $page = $goodsList->render();

        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->assign('goodsList',$goodsList);

        $tpl = input('tpl', 'search_goods');
        return $this->fetch($tpl);
    }
}