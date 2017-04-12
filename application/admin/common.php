<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 17:33
 */
use think\Db;

/**
 * 管理员操作记录
 * @param $log_info
 */
function  adminLog($log_info){
    $add['log_time'] = time();
    $add['admin_id'] = session('admin_id');
    $add['log_info'] = $log_info;
    $add['log_url'] = request()->baseUrl() ;
    Db::name('admin_log')->insert($add);
}

function getAdminInfo($admin_id){
    return Db::name('admin')->where('admin_id',$admin_id)->find();
}

/**
 * 导出excel
 * @param $strTable
 * @param $filename
 */
function downloadExcel($strTable,$filename)
{
    header("Content-type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
    header('Expires:0');
    header('Pragma:public');
    echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';
}

/**
 * 格式化字节大小
 * @param $size
 * @param string $delimiter
 * @return string
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 根据id获取区域名称
 * @param $regionId
 * @return mixed
 */
function getRegionName($regionId){
    $data = Db::name('region')->where(array('id'=>$regionId))->field('name')->find();
    return $data['name'];
}

/**
 * @param $act_list
 * @return mixed
 */
function getMenuList($act_list){
    //根据角色权限过滤菜单
    $menu_list = getAllMenu();
    if($act_list != 'all'){
        $right = Db::name('system_menu')->where("id", "in", $act_list)->cache(true)->column('right',true);
        foreach ($right as $val){
            $role_right .= $val.',';
        }
        $role_right = explode(',', $role_right);
        foreach($menu_list as $k=>$mrr){
            foreach ($mrr['sub_menu'] as $j=>$v){
                if(!in_array($v['control'].'Controller@'.$v['act'], $role_right)){
                    unset($menu_list[$k]['sub_menu'][$j]);//过滤菜单
                }
            }
        }
    }
    return $menu_list;
}

function getAllMenu(){
    return	array(
        'order' => array('name' => '订单管理', 'icon'=>'fa-money', 'sub_menu' => array(
            array('name' => '订单列表', 'act'=>'index', 'control'=>'Order'),
            array('name' => '订单导出', 'act'=>'export_order', 'control'=>'Order'),
            array('name' => '订单发货', 'act'=>'send_order', 'control'=>'Order'),
        )),
        'goods' => array('name' => '商品管理', 'icon'=>'fa-book', 'sub_menu' => array(
            array('name' => '添加商品', 'act'=>'good_Info', 'control'=>'Goods'),
            array('name' => '商品列表', 'act'=>'good_list', 'control'=>'Goods'),
            array('name' => '商品属性', 'act'=>'goods_attribute_list', 'control'=>'Goods'),
            array('name' => '关联商品','act'=>'goods_relate','control'=>'Comment'),
            array('name' => '商品分类', 'act'=>'category_list', 'control'=>'Goods'),
            array('name' => '商品评价','act'=>'index','control'=>'Comment'),
        )),
        'promotion' => array('name' => '促销管理', 'icon'=>'fa-bell', 'sub_menu' => array(
            array('name' => '抢购管理', 'act'=>'flash_sale', 'control'=>'Promotion'),
            array('name' => '团购管理', 'act'=>'group_buy_list', 'control'=>'Promotion'),
            array('name' => '商品促销', 'act'=>'prom_goods_list', 'control'=>'Promotion'),
            array('name' => '订单促销', 'act'=>'prom_order_list', 'control'=>'Promotion'),
            array('name' => '代金券管理','act'=>'index', 'control'=>'Coupon'),
        )),
        'article' => array('name' => '文章管理', 'icon'=>'fa-comments', 'sub_menu' => array(
            array('name' => '文章列表', 'act'=>'article_List', 'control'=>'Article'),
            array('name' => '文章分类', 'act'=>'category_list', 'control'=>'Article'),
        )),
        'operation' => array('name' => '运营管理', 'icon'=>'fa-cubes', 'sub_menu' => array(
            array('name' => '渠道管理', 'act'=>'channel', 'control'=>'Operation'),
            array('name' => '渠道统计', 'act'=>'channel_statistics', 'control'=>'Operation'),
            array('name' => '订单统计', 'act'=>'order_count', 'control'=>'Order'),
            array('name' => '商品统计', 'act'=>'goods_count', 'control'=>'Goods'),
            array('name' => '专题管理', 'act'=>'project_control', 'control'=>'Operation'),
        )),
        'system' => array('name'=>'系统功能','icon'=>'fa-cog','sub_menu'=>array(
            array('name'=>'网站设置','act'=>'index','control'=>'System'),
            array('name'=>'友情链接','act'=>'link_list','control'=>'Article'),
            array('name'=>'推荐位','act'=>'recommended_area','control'=>'System'),
            array('name'=>'普通文章','act'=>'index','control'=>'Article'),
            array('name'=>'短信账号','act'=>'index','control'=>'SmsTemplate'),
            array('name'=>'发送记录','act'=>'send_log','control'=>'SmsTemplate'),

        )),
        'access' => array('name' => '权限管理', 'icon'=>'fa-gears', 'sub_menu' => array(
            array('name'=>'权限列表','act'=>'right_list','control'=>'System'),
            array('name' => '管理员列表', 'act'=>'admin_list', 'control'=>'Admin'),
            array('name' => '角色管理', 'act'=>'role', 'control'=>'Admin'),
            array('name' => '用户组', 'act'=>'index', 'control'=>'Member'),
            array('name' => '管理员日志', 'act'=>'log', 'control'=>'Admin'),
        )),
    );
}
function getMenuArr(){
    $menuArr = include APP_PATH.'admin/conf/menu.php';
    $act_list = session('act_list');
    if($act_list != 'all' && !empty($act_list)){
        $right = Db::name('system_menu')->where("id in ($act_list)")->cache(true)->column('right',true);
        foreach ($right as $val){
            $role_right .= $val.',';
        }

        foreach($menuArr as $k=>$val){
            foreach ($val['child'] as $j=>$v){
                foreach ($v['child'] as $s=>$son){
                    if(!strpos($role_right,$son['op'].'Controller@'.$son['act'])){
                        unset($menuArr[$k]['child'][$j]['child'][$s]);//过滤菜单
                    }
                }
            }
        }

        foreach ($menuArr as $mk=>$mr){
            foreach ($mr['child'] as $nk=>$nrr){
                if(empty($nrr['child'])){
                    unset($menuArr[$mk]['child'][$nk]);
                }
            }
        }
    }
    return $menuArr;
}
function respose($res){
    exit(json_encode($res));
}