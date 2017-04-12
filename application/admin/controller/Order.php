<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/12
 * Time: 9:40
 */

namespace app\admin\controller;


class Order extends Base
{

    //订单列表
    public function index(){
        echo '订单列表';
    }
    //订单导出
    public function export_order(){
        echo '订单导出';
    }
    //订单发货
    public function send_order(){
        echo '订单发货';
    }

    //订单统计
    public function order_count(){

    }

}