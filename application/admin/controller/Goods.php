<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/5
 * Time: 11:27
 */

namespace app\admin\controller;

use app\admin\model\Goods as GoodsModel;
use app\admin\model\GoodsCategory;
use app\admin\model\GoodsImages;
use think\Request;

class Goods extends Base
{
    public function index(){
        echo '商品控制器';
    }

    public function good_list(){
        $goods = new GoodsModel();
        //$condition=array();
        $goodsList = $goods
            ->order('goods_id desc')
            ->paginate(10);
        $count = $goods->count();
        $page = $goodsList->render();
        $this->assign('goodsList',$goodsList);
        $this->assign('page',$page);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //商品详情
    public function good_Info(){
        $good_category = new GoodsCategory();
        $good = new GoodsModel();
        $goods_images = new GoodsImages();

        //自动生成商品编号
        $maxId= $good->max('goods_id');
        $goods_sn='TP'.str_pad(($maxId+1),7,'0',STR_PAD_LEFT);

        if(Request::instance()->isPost()){

            $data['goods_name']=input('goods_name');
            $data['goods_sn']=input('goods_sn')?input('goods_sn'):$goods_sn;
            $data['cat_id']=input('cat_id');
            $data['market_price']=input('market_price');
            $data['shop_price']=input('shop_price');
            $data['store_count']=input('store_count');
            $data['sort']=input('sort');
            $data['on_time']=input('on_time');
            $data['is_on_sale']=input('is_on_sale');
            $data['keywords']=input('keywords');
            $data['goods_remark']=input('goods_remark');
            $data['goods_content']=input('editorValue');
            $data['img']=input('main_img');

            if(empty(input('goods_id'))){//添加商品
                $good->data($data);
                $res = $good->save();

            }else{//编辑商品
                $res =  $good->where('goods_id',input('goods_id'))->update($data);

            }
            if($res){
                //保存细节图 goods_images 到数据库
                $goods_images = input('goods_images/a');
                $maxId = GoodsModel::max('goods_id');
                //初始化细节图表
                GoodsImages::destroy(['goods_id'=>$maxId]);

                $list =array();
                foreach ($goods_images as $val){
                   array_push($list, ['goods_id'=>$maxId,'image_url'=>$val]);
                }
                $goodsImages = new GoodsImages();
                $goodsImages->saveAll($list);

                $this->success('编辑商品成功',Url('good_list'));
            }else{
                $this->error('编辑商品失败',Url('good_Info'));
            }

        }
        $id = input('id');
        if(!empty($id)){//修改商品信息
            $info=$good::get($id);

            //应该用关联会好点(先实现功能)
            $info['cate'] =$good_category->find($info['cat_id'])['name'];
            $goodsImages = $goods_images->where("goods_id = $id")->select();
        }else{//添加商品
            $info=[
                'goods_id'=>'','goods_name'=>'','goods_sn'=>'','cat_id'=>'','market_price'=>'','shop_price'=>'','store_count'=>'','sort'=>'','on_time'=>'','is_on_sale'=>'','keywords'=>'','goods_remark'=>'','goods_content'=>'','img'=>'' ];
            $goodsImages=[];
        }

        $cate = $good_category->select();

        $this->assign('goodsImages',$goodsImages);
        $this->assign('cate',$cate);
        $this->assign('info',$info);
        return $this->fetch();
    }

    //删除
    public function good_del(){
        $id=input('param.id');
        GoodsModel::destroy($id);
        GoodsImages::destroy(['goods_id'=>$id]);
    }

    //是否上架
    public function is_on_sale(){
        $status = input('status');
        $goodsId = input('goods_id');
        $good = new GoodsModel();
        $good->where('goods_id',$goodsId)
            ->update(['is_on_sale'=>$status]);

    }


    //图片上传
    public function img_upload(){
        // 移动到框架应用根目录/public/uploads/ 目录下
        $imgRule=UPLOAD_PATH. 'goods'. DS . 'img'. DS ;

        if(request()->file('img')){//主图处理
            $file_img = request()->file('img');
            $info = $file_img->move($imgRule);
            if($info){
                $res='http://'.$_SERVER['HTTP_HOST'].DS.$imgRule.$info->getSaveName();
                echo json_encode($res);
            }else{
                // 上传失败获取错误信息
                echo json_encode($file_img->getError());
            }
        }elseif(request()->file('goodsImages')){//细节图片上传处理
            $id=input('goods_id');
            $file_image = request()->file('goodsImages');
            $info = $file_image->move($imgRule);
            if($info){
                $res='http://'.$_SERVER['HTTP_HOST'].DS.$imgRule.$info->getSaveName();
                echo json_encode($res);
            }else{
                // 上传失败获取错误信息
                echo json_encode($file_image->getError());
            }
        }else{
            echo json_encode('未知图片资源');
            return;
        };
    }

    //商品属性
    public function goods_attribute_list(){
        echo '商品属性';
    }
    //商品分类
    public function category_list(){
        echo '商品分类';
    }
    //关联商品
    public function goods_relate(){
        echo '关联商品';
    }
    //商品统计
    public function goods_count(){

    }

}