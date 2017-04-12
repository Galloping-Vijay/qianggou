<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/11
 * Time: 10:34
 */
return	array(

    'order'=>array('name'=>'订单管理','child'=>array(
        array('name' => '订单列表', 'act'=>'index', 'op'=>'Order'),
        array('name' => '订单导出', 'act'=>'export_order', 'op'=>'Order'),
        array('name' => '订单发货', 'act'=>'send_order', 'op'=>'Order'),
    )),
    'goods'=>array('name'=>'商品管理','child'=>array(
        array('name' => '添加商品', 'act'=>'good_Info', 'op'=>'Goods'),
        array('name' => '商品列表', 'act'=>'good_list', 'op'=>'Goods'),
        array('name' => '商品属性', 'act'=>'goods_attribute_list', 'op'=>'Goods'),
        array('name' => '关联商品','act'=>'goods_relate','op'=>'Comment'),
        array('name' => '商品分类', 'act'=>'category_list', 'op'=>'Goods'),
        array('name' => '商品评价','act'=>'index','op'=>'Comment'),
    )),
    'promotion'=>array('name'=>'促销管理','child'=>array(
        array('name' => '抢购管理', 'act'=>'flash_sale', 'op'=>'Promotion'),
        array('name' => '团购管理', 'act'=>'group_buy_list', 'op'=>'Promotion'),
        array('name' => '商品促销', 'act'=>'prom_goods_list', 'op'=>'Promotion'),
        array('name' => '订单促销', 'act'=>'prom_order_list', 'op'=>'Promotion'),
        array('name' => '代金券管理','act'=>'index', 'op'=>'Coupon'),
    )),
    'article'=>array('name'=>'文章管理','child'=>array(
        array('name' => '文章列表', 'act'=>'article_List', 'op'=>'Article'),
        array('name' => '文章分类', 'act'=>'category_list', 'op'=>'Article'),
    )),
    'operation'=>array('name'=>'运营管理','child'=>array(
        array('name' => '渠道管理', 'act'=>'channel', 'op'=>'Operation'),
        array('name' => '渠道统计', 'act'=>'channel_statistics', 'op'=>'Operation'),
        array('name' => '订单统计', 'act'=>'order_count', 'op'=>'Order'),
        array('name' => '商品统计', 'act'=>'goods_count', 'op'=>'Goods'),
        array('name' => '专题管理', 'act'=>'project_op', 'op'=>'Operation'),
    )),
    'system'=>array('name'=>'系统功能','child'=>array(
        array('name'=>'网站设置','act'=>'index','op'=>'System'),
        array('name'=>'友情链接','act'=>'link_list','op'=>'Article'),
        array('name'=>'推荐位','act'=>'recommended_area','op'=>'System'),
        array('name'=>'普通文章','act'=>'index','op'=>'Article'),
        array('name'=>'短信账号','act'=>'index','op'=>'SmsTemplate'),
        array('name'=>'发送记录','act'=>'send_log','op'=>'SmsTemplate'),
    )),
    'access'=>array('name'=>'权限管理','child'=>array(
        array('name'=>'权限列表','act'=>'right_list','op'=>'System'),
        array('name' => '管理员列表', 'act'=>'admin_list', 'op'=>'Admin'),
        array('name' => '角色管理', 'act'=>'role', 'op'=>'Admin'),
        array('name' => '用户组', 'act'=>'index', 'op'=>'Member'),
        array('name' => '管理员日志', 'act'=>'log', 'op'=>'Admin'),
    )),

);