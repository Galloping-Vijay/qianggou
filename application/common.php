<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\db;
// 应用公共文件
/**
 * 根据 order_goods 表扣除商品库存
 * @param type $order_id  订单id
 */
function minus_stock($order_id){
    $orderGoodsArr = M('OrderGoods')->where("order_id", $order_id)->select();
    foreach($orderGoodsArr as $key => $val)
    {
        // 有选择规格的商品
        if(!empty($val['spec_key']))
        {   // 先到规格表里面扣除数量 再重新刷新一个 这件商品的总数量
            M('SpecGoodsPrice')->where("goods_id = :goods_id and `key` = :key")->bind(['goods_id'=>$val['goods_id'],'key'=>$val['spec_key']])->setDec('store_count',$val['goods_num']);
            refresh_stock($val['goods_id']);
        }else{
            M('Goods')->where("goods_id", $val['goods_id'])->setDec('store_count',$val['goods_num']); // 直接扣除商品总数量
        }
        M('Goods')->where("goods_id", $val['goods_id'])->setInc('sales_sum',$val['goods_num']); // 增加商品销售量
        //更新活动商品购买量
        if($val['prom_type']==1 || $val['prom_type']==2){
        	$prom = get_goods_promotion($val['goods_id']);
        	if($prom['is_end']==0){
        		$tb = $val['prom_type']==1 ? 'flash_sale' : 'group_buy';
        		M($tb)->where("id", $val['prom_id'])->setInc('buy_num',$val['goods_num']);
        		M($tb)->where("id", $val['prom_id'])->setInc('order_num');
        	}
        }
    }
}

/**
 * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getCatGrandson ($cat_id)
{
    $GLOBALS['catGrandson'] = array();
    $GLOBALS['category_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['catGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['category_id_arr'] = Db::name('GoodsCategory')->cache(true,TPSHOP_CACHE_TIME)->Field('id,parent_id')->select();
    // 先把所有儿子找出来
    $son_id_arr = Db::name('GoodsCategory')->where("parent_id", $cat_id)->cache(true,TPSHOP_CACHE_TIME)->Field('id',true)->select();
    foreach($son_id_arr as $k => $v)
    {
        getCatGrandson2($v);
    }
    return $GLOBALS['catGrandson'];
}

/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getCatGrandson2($cat_id)
{
    $GLOBALS['catGrandson'][] = $cat_id;
    foreach($GLOBALS['category_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getCatGrandson2($k); // 继续找孙子
        }
    }
}

/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getArticleCatGrandson2($cat_id)
{
    $GLOBALS['ArticleCatGrandson'][] = $cat_id;
    foreach($GLOBALS['cat_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getArticleCatGrandson2($k); // 继续找孙子
        }
    }
}

/**
 * 获取商品库存
 * @param type $goods_id 商品id
 * @param type $key  库存 key
 */
function getGoodNum($goods_id,$key)
{
    if(!empty($key))
        return M("SpecGoodsPrice")->where(['goods_id' => $goods_id, 'key' => $key])->getField('store_count');
    else
        return  M("Goods")->where("goods_id", $goods_id)->getField('store_count');
}

/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array or string or bool
 */
function tpCache($config_key,$data = array()){
    $param = explode('.', $config_key);
    if(empty($data)){
        //如$config_key=shop_info则获取网站信息数组
        //如$config_key=shop_info.logo则获取网站logo字符串
        $config = F($param[0],'',TEMP_PATH);//直接获取缓存文件
        if(empty($config)){
            //缓存文件不存在就读取数据库
            $res = D('config')->where("inc_type",$param[0])->select();
            if($res){
                foreach($res as $k=>$val){
                    $config[$val['name']] = $val['value'];
                }
                F($param[0],$config,TEMP_PATH);
            }
        }
        if(count($param)>1){
            return $config[$param[1]];
        }else{
            return $config;
        }
    }else{
        //更新缓存
        $result =  D('config')->where("inc_type", $param[0])->select();
        if($result){
            foreach($result as $val){
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k=>$v){
                $newArr = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
                if(!isset($temp[$k])){
                    M('config')->add($newArr);//新key数据插入数据库
                }else{
                    if($v!=$temp[$k])
                        M('config')->where("name", $k)->save($newArr);//缓存key存在且值有变更新此项
                }
            }
            //更新后的数据库记录
            $newRes = D('config')->where("inc_type", $param[0])->select();
            foreach ($newRes as $rs){
                $newData[$rs['name']] = $rs['value'];
            }
        }else{
            foreach($data as $k=>$v){
                $newArr[] = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
            }
            M('config')->insertAll($newArr);
            $newData = $data;
        }
        return F($param[0],$newData,TEMP_PATH);
    }
}
/**
 * 查看商品是否有活动
 * @param goods_id 商品ID
 */

function get_goods_promotion($goods_id,$user_id=0){
	$now = time();
	$goods = M('goods')->where("goods_id", $goods_id)->find();
    $where = [
        'end_time' => ['gt', $now],
        'start_time' => ['lt', $now],
        'id' => $goods['prom_id'],
    ];

	$prom['price'] = $goods['shop_price'];
	$prom['prom_type'] = $goods['prom_type'];
	$prom['prom_id'] = $goods['prom_id'];
	$prom['is_end'] = 0;

	if($goods['prom_type'] == 1){//抢购
		$prominfo = M('flash_sale')->where($where)->find();
		if(!empty($prominfo)){
			if($prominfo['goods_num'] == $prominfo['buy_num']){
				$prom['is_end'] = 2;//已售馨
			}else{
				//核查用户购买数量
				$where = "user_id = :user_id and order_status!=3 and  add_time>".$prominfo['start_time']." and add_time<".$prominfo['end_time'];
				$order_id_arr = M('order')->where($where)->bind(['user_id'=>$user_id])->getField('order_id',true);
				if($order_id_arr){
					$goods_num = M('order_goods')->where("prom_id={$goods['prom_id']} and prom_type={$goods['prom_type']} and order_id in (".implode(',', $order_id_arr).")")->sum('goods_num');
					if($goods_num < $prominfo['buy_limit']){
						$prom['price'] = $prominfo['price'];
					}
				}else{
					$prom['price'] = $prominfo['price'];
				}
			}
		}
	}

	if($goods['prom_type']==2){//团购
		$prominfo = M('group_buy')->where($where)->find();
		if(!empty($prominfo)){
			if($prominfo['goods_num'] == $prominfo['buy_num']){
				$prom['is_end'] = 2;//已售馨
			}else{
				$prom['price'] = $prominfo['price'];
			}
		}
	}
	if($goods['prom_type'] == 3){//优惠促销
		$parse_type = array('0'=>'直接打折','1'=>'减价优惠','2'=>'固定金额出售','3'=>'买就赠优惠券','4'=>'买M件送N件');
		$prominfo = M('prom_goods')->where($where)->find();
		if(!empty($prominfo)){
			if($prominfo['type'] == 0){
				$prom['price'] = $goods['shop_price']*$prominfo['expression']/100;//打折优惠
			}elseif($prominfo['type'] == 1){
				$prom['price'] = $goods['shop_price']-$prominfo['expression'];//减价优惠
			}elseif($prominfo['type']==2){
				$prom['price'] = $prominfo['expression'];//固定金额优惠
			}
		}
	}

	if(!empty($prominfo)){
		$prom['start_time'] = $prominfo['start_time'];
		$prom['end_time'] = $prominfo['end_time'];
	}else{
		$prom['prom_type'] = $prom['prom_id'] = 0 ;//活动已过期
		$prom['is_end'] = 1;//已结束
	}

	if($prom['prom_id'] == 0){
		M('goods')->where("goods_id", $goods_id)->save($prom);
	}
	return $prom;
}

/**
 * 查看订单是否满足条件参加活动
 * @param order_amount 订单应付金额
 */
function get_order_promotion($order_amount){
	$parse_type = array('0'=>'满额打折','1'=>'满额优惠金额','2'=>'满额送倍数积分','3'=>'满额送优惠券','4'=>'满额免运费');
	$now = time();
	$prom = M('prom_order')->where("type<2 and end_time>$now and start_time<$now and money<=$order_amount")->order('money desc')->find();
	$res = array('order_amount'=>$order_amount,'order_prom_id'=>0,'order_prom_amount'=>0);
	if($prom){
		if($prom['type'] == 0){
			$res['order_amount']  = round($order_amount*$prom['expression']/100,2);//满额打折
			$res['order_prom_amount'] = $order_amount - $res['order_amount'] ;
			$res['order_prom_id'] = $prom['id'];
		}elseif($prom['type'] == 1){
			$res['order_amount'] = $order_amount- $prom['expression'];//满额优惠金额
			$res['order_prom_amount'] = $prom['expression'];
			$res['order_prom_id'] = $prom['id'];
		}
	}
	return $res;
}
/**
 * 获取商品一二三级分类
 * @return type
 */
function get_goods_category_tree(){
	$result = array();
	$cat_list = M('goods_category')->where("is_show = 1")->order('sort_order')->cache(true)->select();//所有分类

	foreach ($cat_list as $val){
		if($val['level'] == 2){
			$arr[$val['parent_id']][] = $val;
		}
		if($val['level'] == 3){
			$crr[$val['parent_id']][] = $val;
		}
		if($val['level'] == 1){
			$tree[] = $val;
		}
	}

	foreach ($arr as $k=>$v){
		foreach ($v as $kk=>$vv){
			$arr[$k][$kk]['sub_menu'] = empty($crr[$vv['id']]) ? array() : $crr[$vv['id']];
		}
	}

	foreach ($tree as $val){
		$val['tmenu'] = empty($arr[$val['id']]) ? array() : $arr[$val['id']];
		$result[$val['id']] = $val;
	}
	return $result;
}

/**
 * 写入静态页面缓存
 */
function write_html_cache($html){
    $html_cache_arr = C('HTML_CACHE_ARR');
    $request = think\Request::instance();
    $m_c_a_str = $request->module().'_'.$request->controller().'_'.$request->action(); // 模块_控制器_方法
    $m_c_a_str = strtolower($m_c_a_str);
    //exit('write_html_cache写入缓存<br/>');
    foreach($html_cache_arr as $key=>$val)
    {
        $val['mca'] = strtolower($val['mca']);
        if($val['mca'] != $m_c_a_str) //不是当前 模块 控制器 方法 直接跳过
            continue;

        if(!is_dir(RUNTIME_PATH.'html'))
                mkdir(RUNTIME_PATH.'html');
        $filename =  RUNTIME_PATH.'html'.DIRECTORY_SEPARATOR.$m_c_a_str;
        // 组合参数
        if(isset($val['p']))
        {
            foreach($val['p'] as $k=>$v)
                $filename.='_'.$_GET[$v];
        }
        $filename.= '.html';
        file_put_contents($filename, $html);
    }
}

/**
 * 读取静态页面缓存
 */
function read_html_cache(){
    $html_cache_arr = C('HTML_CACHE_ARR');
    $request = think\Request::instance();
    $m_c_a_str = $request->module().'_'.$request->controller().'_'.$request->action(); // 模块_控制器_方法
    $m_c_a_str = strtolower($m_c_a_str);
    //exit('read_html_cache读取缓存<br/>');
    foreach($html_cache_arr as $key=>$val)
    {
        $val['mca'] = strtolower($val['mca']);
        if($val['mca'] != $m_c_a_str) //不是当前 模块 控制器 方法 直接跳过
            continue;

        $filename =  RUNTIME_PATH.'html'.DIRECTORY_SEPARATOR.$m_c_a_str;
        // 组合参数
        if(isset($val['p']))
        {
            foreach($val['p'] as $k=>$v)
                $filename.='_'.$_GET[$v];
        }
        $filename.= '.html';
        if(file_exists($filename))
        {
            echo file_get_contents($filename);
            exit();
        }
    }
}
